create view bezirk as 
select 
 altona.date, 
 altona.value as altona, 
 bergedorf.value as bergedorf, 
 eimsbuettel.value as eimsbuettel, 
 mitte.value as mitte,
 nord.value as nord,
 harburg.value as harburg,
 wandsbek.value as wandsbek
 
 from 
 (select date, value from data where id_column=26) as altona,
 (select date, value from data where id_column=27) as bergedorf,
 (select date, value from data where id_column=28) as eimsbuettel,
 (select date, value from data where id_column=29) as mitte,
 (select date, value from data where id_column=30) as nord,
 (select date, value from data where id_column=31) as harburg,
 (select date, value from data where id_column=32) as wandsbek
 
 WHERE  
   altona.date=bergedorf.date and 
   bergedorf.date=eimsbuettel.date and 
   eimsbuettel.date=mitte.date and 
   mitte.date = nord.date AND
   nord.date=harburg.date AND
   harburg.date=wandsbek.date;
   
   
   
   