<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */


class DbConsoleProfiler extends DbProfiler
{

	private $_displayProfile = false;

	public function setDisplayProfile($displayProfile)
	{
		$this->_displayProfile = $displayProfile;
	}

	public function beforeStartProfile(DbProfilerItem $profile)
	{
		if($this->_displayProfile==true){
			echo $profile->getInitialTime(), ': ', str_replace(array("\n", "\t"), " ", $profile->getSQLStatement());
		}
	}

	public function afterEndProfile($profile)
	{
		if($this->_displayProfile==true){
			echo '  => ', $profile->getFinalTime(), ' (', ($profile->getTotalElapsedSeconds()), ')', '<br/>';
		}
	}

}

class HfosConsole extends UserComponent
{

	public function __construct()
	{

	}

	private function _formatOutput($out)
	{
		return nl2br(str_replace(' ', '&nbsp;', htmlentities($out)));
	}

	public function _executeRealCommand($command)
	{
		ob_start();
		system($command, $var);
		echo $this->_formatOutput(ob_get_clean());
	}

	private function _renderResult($connection, $profiler, $result, $affectedRows)
	{
		$number = 0;
		if ($affectedRows > 0) {
			$rows = array(
				'weight' => array(),
				'headers' => array(),
				'data' => array()
			);
			$firstRow = true;
			$numberColumns = 0;
			$connection->setFetchMode(DbBase::DB_NUM);
			while($row = $connection->fetchArray($result)){
				if($firstRow==true){
					foreach($row as $key => $value){
						$fieldName = $connection->fieldName($key, $result);
						$rows['weight'][$key] = strlen($fieldName);
						$rows['headers'][$key] = $fieldName;
						$numberColumns++;
					}
					$firstRow = false;
				}
				foreach($row as $key => $value){
					if(strlen($value)>$rows['weight'][$key]){
						$rows['weight'][$key] = strlen($value);
					}
					$rows['data'][$number][$key] = $value;
				}
				$number++;
			}
		}
		if ($number > 0) {
			$this->_renderTable($rows, $numberColumns);
			echo $number . ' rows in set ';
		} else {
			echo 'Empty set ';
		}
		$profile = $profiler->getLastProfile();
		if($profile){
			echo '(', Currency::number($profile->getTotalElapsedSeconds(), 2), ' sec)';
		} else {
			echo '(0.00 sec)';
		}
		echo '<br/>', '<br/>';
	}

	private function _renderTable($table, $numberColumns){
		echo '<pre>';
		echo '+';
		$totalLength = 0;
		for($i=0;$i<$numberColumns;$i++){
			echo str_repeat('-', $table['weight'][$i]+2);
			if($numberColumns!=($i+1)){
				echo '-';
			}
			$totalLength+=$table['weight'][$i]+2;
		}
		echo '+', '<br/>';
		echo '|';
		for($i=0;$i<$numberColumns;$i++){
			$padLength = intval(($table['weight'][$i]-strlen($table['headers'][$i]))/2);
			$padDiff = ($table['weight'][$i]-strlen($table['headers'][$i]))-$padLength*2;
			echo ' ', str_repeat(' ', $padLength), $table['headers'][$i], str_repeat(' ', $padLength+$padDiff), ' ';
			if($numberColumns!=($i+1)){
				echo '|';
			}
		}
		echo '|', '<br/>';
		echo '+';
		for($i=0;$i<$numberColumns;$i++){
			echo str_repeat('-', $table['weight'][$i]+2);
			if($numberColumns!=($i+1)){
				echo '-';
			}
		}
		echo '+', '<br/>';
		foreach($table['data'] as $row){
			echo '|';
			foreach($row as $key => $value){
				$padLength = intval($table['weight'][$key]-strlen($value));
				if(is_numeric($value)){
					echo ' ', str_repeat(' ', $padLength), $value, ' ';
				} else {
					echo ' ', $value, str_repeat(' ', $padLength), ' ';
				}
				if($numberColumns!=($key+1)){
					echo '|';
				}
			}
			echo '|', '<br/>';
		}
		echo '+';
		for ($i = 0; $i < $numberColumns; $i++) {
			echo str_repeat('-', $table['weight'][$i]+2);
			if ($numberColumns != ($i + 1)) {
				echo '-';
			}
		}
		echo '+', '<br/>';
		echo '</pre>';
	}

	private function _showEmptySet()
	{
		$profile = $profiler->getLastProfile();
		if($profile){
			echo 'Empty set (', LocaleMath::round($profile->getTotalElapsedSeconds(), 2), ' sec)', '<br/>', '<br/>';
		} else {
			echo 'Empty set (0.00 sec)', '<br/>', '<br/>';
		}
	}

	private function _executeSQL($sql){
		try {

			$confirm = false;
			if(preg_match('#/Y$#', $sql)){
				$sql = preg_replace('#/Y$#', '', $sql);
				$confirm = true;
			}

			$connection = DbPool::getConnection();
			$profiler = new DbConsoleProfiler();
			$connection->setProfiling($profiler);
			$result = $connection->query($sql);
			$numberRows = $connection->numRows($result);
			if($numberRows>0){
				if($numberRows<250||$confirm){
					$this->_renderResult($connection, $profiler, $result, $numberRows);
				} else {
					echo '!! Result will dump '.$numberRows.' rows to console, finish command with /Y to confirm <br/><br/>';
				}
			} else {
				$affectedRows = $connection->affectedRows();
				echo 'Query OK, '.$affectedRows.' rows affected (', LocaleMath::round($profiler->getLastProfile()->getTotalElapsedSeconds(), 2), ' sec)', '<br/>', '<br/>';
			}
		}
		catch(CoreException $e){
			echo get_class($e), ' : ', $e->getConsoleMessage(), '<br/>', '<br/>';
		}
	}

	private function _executeUnload($command){

		$tokens = preg_split('/[ ]/', $command);

		if(isset($tokens[3])){
			if($tokens[3]!='select'){
				echo 'Unload only can export SQL queries', '<br/><br/>';
				return;
			}
		} else {
			echo 'Incomplete unload comand', '<br/><br/>';
			return;
		}

		if(isset($tokens[2])){
			$fileName = str_replace('"', '', $tokens[2]);
			$fileName = strtolower($fileName);
			if(!preg_match('/\.([a-z0-9]+)$/', $fileName, $matches)){
				echo 'Can\'t determine file type for output ('.$fileName.')', '<br/><br/>';
				return;
			}
			switch($matches[1]){
				case 'txt':
				case 'log':
				case 'text':
					$reportType = 'text';
					break;
				case 'xls':
				case 'xlsx':
					$reportType = 'excel';
					break;
				case 'html':
				case 'htm':
					$reportType = 'html';
					break;
				case 'pdf':
					$reportType = 'pdf';
					break;
				default:
					echo 'Unsupported file type "'.$matches[1].'" for unload', '<br/><br/>';
					return;
			}
			$fileName = str_replace('.'.$matches[1], '', $fileName);
		} else {
			echo 'Output file name is require for unload', '<br/><br/>';
			return;
		}

		$connection = DbPool::getConnection();
		$result = $connection->query(join(' ', array_slice($tokens, 3)));

		if($connection->numRows($result)){

			$report = ReportBase::factory($reportType);
  			$report->setDocumentTitle('Unload');

  			$rows = array(
				'headers' => array(),
				'data' => array()
			);
			$number = 0;
			$firstRow = true;
			$numberColumns = 0;
			$connection->setFetchMode(DbBase::DB_NUM);
			while($row = $connection->fetchArray($result)){
				if($firstRow==true){
					foreach($row as $key => $value){
						$fieldName = $connection->fieldName($key, $result);
						$rows['headers'][$key] = $fieldName;
						$numberColumns++;
					}
					$firstRow = false;
				}
				foreach($row as $key => $value){
					$rows['data'][$number][$key] = $value;
				}
				$number++;
				unset($row);
			}

			$report->setColumnHeaders($rows['headers']);

			$report->start(true);
			foreach($rows['data'] as $row){
				$report->addRow($row);
			}

			$report->finish();
			$fileName = $report->outputToFile('public/temp/'.$fileName);

			echo 'Exporting succesfull to '.$fileName;

			echo '<script type="text/javascript">window.open("'.Core::getInstancePath()."temp/".$fileName.'")</script>';

		} else {
			echo 'There are no data to export', '<br/><br/>';
			return;
		}

	}

	private function _executeGc($code)
	{

		if($code[1][0]=='VAR'){
			switch($code[1][1]){
				case 'metadata':
					GarbageCollector::freeAllMetaData();
					echo 'Freed models meta-data <br/>';
					return;
				case 'standardform':
					GarbageCollector::freeAllStdForm();
					echo 'Freed standard form meta-data <br/>';
					return;
				case 'all':
					GarbageCollector::startCollect(true);
					echo 'Freed all posible meta-data <br/>';
					return;
			}
		}

		echo 'Unknown type of collect "'.$code[1][1].'"<br/><br/>';
		return;

	}

	private function _executeGrant($code)
	{

		//Privileges granted
		$privileges = array(
			'all' => 1,
			'add' => 1,
			'modify' => 1,
			'select' => 1,
			'copy' => 1,
			'delete' => 1,
		);

		$node = $code[1];
		$privList = array();
		while($node[0]=='PRIV_LIST'){
			if($node[2][0]=='VAR'){
				$privilege = $node[2][1];
				if(!isset($privileges[$privilege])){
					throw new HfosCommandRunnerException('Unsupported privilege "'.$privilege.'" for grant command');
				}
				array_unshift($privList, $privilege);
			}
			$node = $node[1];
		}
		if($node[0]=='VAR'){
			$privilege = $node[1];
			if(!isset($privileges[$privilege])){
				throw new HfosCommandRunnerException('Unsupported privilege "'.$privilege.'" for grant command');
			}
			array_unshift($privList, $privilege);
		}

		if(in_array('all', $privList)){
			$grantPrivileges = array('A', 'M', 'S', 'D', 'R');
		} else {
			$grantPrivileges = array();
			$posiblePerms = array('add' => 'A', 'modify' => 'M', 'select' => 'S', 'delete' => 'D', 'copy' => 'R');
			foreach($privList as $privilege){
				$grantPrivileges[] = $posiblePerms[$privilege];
			}
		}

		//On comprobs
		$node = $code[2];
		$comprobList = array();
		while($node[0]=='COMPROB_LIST'){
			if($node[2][0]=='VAR'){
				$codigoComprob = $node[2][1];
				array_unshift($comprobList, $codigoComprob);
			}
			$node = $node[1];
		}
		if($node[0]=='VAR'){
			$codigoComprob = $node[1];
			array_unshift($comprobList, $codigoComprob);
		}

		foreach($comprobList as $codigoComprob){
			if($codigoComprob!='all'){
				$comprob = BackCacher::getComprob($codigoComprob);
				if($comprob==false){
					throw new HfosCommandRunnerException('Unknown comprob "'.$codigoComprob.'" for grant command');
				}
			}
		}

		//To User
		if($code[3][0]=='LITERAL'){
			$login = $this->filter($code[3][2], 'usuario');
		} else {
			$login = $this->filter($code[3][1], 'usuario');
		}
		$usuario = $this->Usuarios->findFirst("login='$login' AND estado='A'");
		if($usuario==false){
			throw new HfosCommandRunnerException('There is no user with login "'.$login.'" or is inactive');
		}

		try {

			$transaction = TransactionManager::getUserTransaction();

			if(in_array('all', $comprobList)){

				$this->PermisosComprob->setTransaction($transaction);
				$permisosComprob = $this->PermisosComprob->find("usuarios_id='{$usuario->getId()}'");
				foreach($permisosComprob as $permisoComprob){
					if($permisoComprob->delete()==false){
						foreach ($permisoComprob->getMessages() as $messages){
							$transaction->rollback('Cleaning perms: '.$messages->getMessage());
						}
					}
				}

				foreach($this->Comprob->find() as $comprob){
					foreach($grantPrivileges as $privilege){
						$permisosComprob = new PermisosComprob();
						$permisosComprob->setTransaction($transaction);
						$permisosComprob->setUsuariosId($usuario->getId());
						$permisosComprob->setComprob($comprob->getCodigo());
						$permisosComprob->setPopcion($privilege);
						if($permisosComprob->save()==false){
							foreach ($permisosComprob->getMessages() as $messages){
								$transaction->rollback('Saving perms: '.$messages->getMessage());
							}
						}
					}
				}
				echo 'Privileges successfully granted to '.$usuario->getLogin(), ' on all comprobs<br/>';
			} else {
				foreach($comprobList as $codigoComprob){
					foreach($grantPrivileges as $privilege){
						$permisosComprob = new PermisosComprob();
						$permisosComprob->setTransaction($transaction);
						$permisosComprob->setUsuariosId($usuario->getId());
						$permisosComprob->setComprob($codigoComprob);
						$permisosComprob->setPopcion($privilege);
						if($permisosComprob->save()==false){
							foreach ($permisosComprob->getMessages() as $messages){
								$transaction->rollback($messages->getMessage());
							}
						}
					}
					$comprob = BackCacher::getComprob($codigoComprob);
					echo 'Privileges successfully granted to '.$usuario->getLogin(), ' on comprob ',
						$codigoComprob, '/', $comprob->getNomComprob(), '<br/>';
				}

			}

			$transaction->commit();
		}
		catch(TransactionFailed $e){
			echo 'Transaction Rollback: ', $e->getMessage();
		}

	}

	private function _executePs()
	{
		$sql = "SELECT user_session.id AS PID,
		usuarios.login as UID,
		app_code as Process,
		state as State,
		FROM_UNIXTIME(ping_time) AS 'Last Ping'
		FROM hfos_workspace.user_session, usuarios
		WHERE usuarios.id = user_session.usuarios_id
		ORDER BY ping_time DESC";
		$this->_executeSQL($sql);
	}

	private function _executeRestartService($code)
	{

		if (PHP_OS != 'Linux') {
			echo '!! Services only can be restarted in Linux hosts<br/><br/>';
			return;
		}
		if (!file_exists('/etc/sudoers')) {
			echo '!! Sudoers file doesn\'t exists. HFOS requires sudoers NOPASSWD for wwwrun<br/><br/>';
			return;
		}

		if($code[1][0]=='VAR'){
			$permitted = array(
				'apache2' => 1,
				'mysql' => 1,
				'xinetd' => 1
			);
			$service = $code[1][1];
			if(isset($permitted[$service])){
				echo 'Restarting service in background... <br/>';
				$this->_executeRealCommand('/sbin/service '.$service.' restart');
			} else {
				echo '!! You aren\'t authorized to restart service '.$service, '<br/><br/>';
				return;
			}
		}

	}

	private function _executeDiskUsage()
	{
		$this->_executeRealCommand('df -H');
		return;
	}

	private function _executeUptime()
	{
		$this->_executeRealCommand('uptime');
		return;
	}

	public function _executeShow($code)
	{
		if ($code[1][1]=='loaded' && $code[2][1]=='extensions') {
			echo $this->_formatOutput(print_r(get_loaded_extensions(), true));
			return;
		}

		echo '!! No information to show<br/><br/>';
		return;
	}

	public function executeCommand($command)
	{

		//Cancel command
		if (preg_match('/\\\c/', $command)) {
			return;
		}

		if (!preg_match('/^auth /i', $command)) {
			if (!Session::get('consoleAuth')) {
				echo 'Use command "auth" to start a console session<br/>';
				return;
			}
		}

		set_time_limit(0);

		//SQL commands
		if (preg_match('/^select /i', $command)) {
			return $this->_executeSQL($command);
		}
		if (preg_match('/^insert /i', $command)) {
			return $this->_executeSQL($command);
		}
		if (preg_match('/^update /i', $command)) {
			return $this->_executeSQL($command);
		}
		if (preg_match('/^delete /i', $command)) {
			return $this->_executeSQL($command);
		}
		if(preg_match('/^desc /i', $command)){
			return $this->_executeSQL($command);
		}
		if(preg_match('/^alter /i', $command)){
			echo '!! SQL/DDL operations are not supported<br/><br/>';
			return;
		}
		if(preg_match('/^create table /i', $command)){
			echo '!! SQL/DDL operations are not supported<br/><br/>';
			return;
		}

		//Unload SQL query to file
		if (preg_match('/^unload to /i', $command)) {
			return $this->_executeUnload($command);
		}

		//Others commands in compiler
		try {
			HfosCommandRunner::parse($command);
			$this->_executeOpCode(HfosCommandRunner::getOpCode());
		} catch (CoreException $e) {
			echo get_class($e), ' : ', $e->getConsoleMessage(), '<br/>', '<br/>';
			return;
		}

	}

	private function _executeWhoami()
	{
		echo $this->_formatOutput(print_R(IdentityManager::getActive(), true));
		return;
	}

	private function _executeDate()
	{
		echo date('r');
		return;
	}

	private function _executeBackup($code){
		if(PHP_OS!='Linux'){
			echo '!! Backup only can be doing in Linux hosts<br/><br/>';
			return;
		}
		if(isset($code[1])){
			echo $code[1];
		}
	}

	private function _executeShowHelp(){
		echo '<pre>';
		$list = array(
			'SQL' => array(
				'insert into `table` ...' => 'Performs a database INSERT operation using SQL syntax',
				'update `table` ...' => 'Performs a database UPDATE operation using SQL syntax',
				'delete from `table` ...' => 'Performs a database DELETE operation using SQL syntax',
				'select ...' => 'Performs a database SQL query',
				'unload to file select ...' => 'Unloads resulset to a plain file',
			),
			'Garbage Collect' => array(
				'gc all' => 'Collects and free all posible resident and temporal memory',
				'gc metadata' => 'Collects and free all table schema related memory',
				'gc standardform' => 'Collects and free all standard form metadata in active session',
			),
			'Network' => array(
				'restart service name' => 'Restarts a service',
			),
			'Backup' => array(
				'backup' => 'Instant local standard backup for main databases',
			),
			'Enviroment Information' => array(
				'show loaded extensions' => 'Shows loaded PHP extensions',
			),
			'Utility' => array(
				'whoami' => 'Displays effective user information',
				'ps' => 'Displays all user sessions information',
				'uptime' => 'Shows how long time system has been running',
				'disk usage' => 'Shows active disks usage',
				'date' => 'Shows server date and time',
				'clear' => 'Clears screen',
				'history' => 'Shows the command history',
			)
		);
		echo sprintf('%-25s', 'Command'), ' ';
		echo 'Description', '<br/>';
		echo str_repeat('-', 90), '<br/>';
		foreach($list as $type => $commands){
			echo $type, ' &gt;', PHP_EOL;
			foreach($commands as $command => $description){
				echo sprintf('%-25s', $command), ' ';
				echo $description, '<br/>';
			}
			echo '<br/>';
		}
		echo '</pre>';
		return;
	}

	private function _executeAura($code)
	{
		if (!Resolver::hasDefinition('contab.aura')) {
			echo '!! There is no naming definition for contab.aura <br/><br/>';
			return;
		}
		$aura = IdentityManager::getAuthedService('contab.aura');
		switch ($code[1][1]) {
			case 'recalculateActive':
				$aura->recalculateBalances();
				break;
			case 'corrigeUnaLinea':
				$aura->corrigeUnaLinea();
				break;
			case 'recalculateSaldosn':
                $aura->recalculateSaldosn();
                break;
			default:
				echo 'Unknown command "' . $code[1][1] . '" for aura', '<br/><br/>';
				return;
		}
	}

	private function _executeTatico($code){
		if ($code[1][0]=='VAR') {

			if (!Resolver::hasDefinition('inve.tatico')) {
				echo '!! There is no naming definition for inve.tatico <br/><br/>';
				return;
			}

			set_time_limit(0);
			$inve = IdentityManager::getAuthedService('inve.tatico');
			switch($code[1][1]){
				case 'recalculate':
					$inve->recalculateBalances();
					break;
				case 'arreglaTraslados':
					print_r($inve->arreglaTraslados());
					break;
				default:
					echo 'Unknown command "' . $code[1][1] . '" for tatico', '<br/><br/>';
					return;
			}
		}
	}

	private function _executeAuth($code)
	{
		if ($code[1][1] == 'Cuzldwfr11') {
			Session::set('consoleAuth', true);
			echo 'INFO: Access to console granted', '<br/><br/>';
		} else {
			echo '!! Incorrect password', '<br/><br/>';
		}
		return;
	}

	public function _executeOpCode($code) {
		switch ($code[0]) {

			case 'AURA':
				$this->_executeAura($code);
				break;
			case 'TATICO':
				$this->_executeTatico($code);
				break;

			case 'GRANT':
				$this->_executeGrant($code);
				break;
			case 'GC':
				$this->_executeGc($code);
				break;
			case 'RESTART_SERVICE':
				$this->_executeRestartService($code);
				break;
			case 'SHOW':
				$this->_executeShow($code);
				break;
			case 'BACKUP':
				$this->_executeBackup($code);
				break;
			case 'DISK_USAGE':
				$this->_executeDiskUsage($code);
				break;
			case 'UPTIME':
				$this->_executeUptime($code);
				break;
			case 'WHOAMI':
				$this->_executeWhoami($code);
				break;
			case 'PS':
				$this->_executePs($code);
				break;
			case 'DATE':
				$this->_executeDate($code);
				break;
			case 'HELP':
				$this->_executeShowHelp();
				break;
			case 'AUTH':
				$this->_executeAuth($code);
				break;
		}
		//echo nl2br(str_replace(' ', '&nbsp;', print_r($code, true)));
	}

}
