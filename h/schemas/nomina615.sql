update maestro set f_nace = current_date - INTERVAL 18 YEAR where (f_nace >= (current_date - INTERVAL 18 YEAR)) OR f_nace IS NULL;
