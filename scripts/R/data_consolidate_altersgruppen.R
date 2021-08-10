
library(tidyverse)
#library(readxl)
library(DBI)
library(RMySQL)
library(lubridate)

# parameter definieren
port=3306
host.corona=""
dbname.corona=""
user.corona="" # nutzer
password.corona="" # passwort

# Verbindung
con.corona <- dbConnect(RMySQL::MySQL(), dbname=dbname.corona,host=host.corona,port=port,user=user.corona,password=password.corona)

# abfragen der benoetigten tabellen
data <- dbGetQuery(con.corona, "SELECT * FROM data")
columns <- dbGetQuery(con.corona, "SELECT * FROM columns")

#datensatz bauen
data.01 <- data%>%
  left_join(columns, by = c("id_column" = "id"))%>%
  mutate(altersangaben = grepl("Verteilung der Infizierten nach Alter und Geschlecht", title, fixed = TRUE))%>%
  filter(altersangaben == TRUE)%>%
  select(date, value, title)%>%
  pivot_wider(names_from = title, values_from = value, values_fill = NA)%>%
  mutate(date = as_date(date, format = "%Y-%m-%d"))%>%
  filter(date >= as_date("2020-10-13", "%Y-%m-%d"))%>%
  mutate(kw = isoweek(date),
         jahr = year(date),
         weekday = weekdays(date))%>%
  rename(cum_plus90_weiblich = `-Verteilung der Infizierten nach Alter und Geschlecht-�ber 90 Jahre-weiblich`,
         cum_plus90_maennlich = `-Verteilung der Infizierten nach Alter und Geschlecht-�ber 90 Jahre-m�nnlich`,
         `cum_80-89_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-80 bis 89 Jahre-weiblich`,
         `cum_80-89_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-80 bis 89 Jahre-m�nnlich`,
         `cum_70-79_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-70 bis 79 Jahre-weiblich`,
         `cum_70-79_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-70 bis 79 Jahre-m�nnlich`,
         `cum_60-69_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-60 bis 69 Jahre-weiblich`,
         `cum_60-69_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-60 bis 69 Jahre-m�nnlich`,
         `cum_50-59_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-50 bis 59 Jahre-weiblich`,
         `cum_50-59_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-50 bis 59 Jahre-m�nnlich`,
         `cum_40-49_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-40 bis 49 Jahre-weiblich`,
         `cum_40-49_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-40 bis 49 Jahre-m�nnlich`,
         `cum_30-39_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-30 bis 39 Jahre-weiblich`,
         `cum_30-39_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-30 bis 39 Jahre-m�nnlich`,
         `cum_20-29_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-20 bis 29 Jahre-weiblich`,
         `cum_20-29_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-20 bis 29 Jahre-m�nnlich`,
         `cum_15-19_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-15 bis 19 Jahre-weiblich`,
         `cum_15-19_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-15 bis 19 Jahre-m�nnlich`,
         `cum_6-14_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-6 bis 14 Jahre-weiblich`,
         `cum_6-14_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-6 bis 14 Jahre-m�nnlich`,
         `cum_bis5_weiblich` = `-Verteilung der Infizierten nach Alter und Geschlecht-bis 5 Jahre-weiblich`,
         `cum_bis5_maennlich` = `-Verteilung der Infizierten nach Alter und Geschlecht-bis 5 Jahre-m�nnlich`)%>%
  arrange(date)%>%
  mutate(delete = ifelse(weekday == "Montag" & `cum_20-29_maennlich` == lag(`cum_20-29_maennlich`) & `cum_20-29_weiblich` == lag(`cum_20-29_weiblich`), TRUE, FALSE))%>%
  filter(delete == FALSE & !(kw == 53  & jahr == 2021))%>%
  select(kw, jahr, cum_plus90_weiblich,cum_plus90_maennlich,`cum_80-89_weiblich`,`cum_80-89_maennlich`,`cum_70-79_weiblich`,
         `cum_70-79_maennlich`,`cum_60-69_weiblich`,`cum_60-69_maennlich`,`cum_50-59_weiblich`,`cum_50-59_maennlich`,`cum_40-49_weiblich`,
         `cum_40-49_maennlich`,`cum_30-39_weiblich`,`cum_30-39_maennlich`,`cum_20-29_weiblich`,`cum_20-29_maennlich`,`cum_15-19_weiblich`,
         `cum_15-19_maennlich`,`cum_6-14_weiblich`,`cum_6-14_maennlich`,`cum_bis5_weiblich`,`cum_bis5_maennlich`)%>%
  distinct()%>%
  arrange(jahr, kw)%>%
  mutate(cum_plus90 = cum_plus90_maennlich + cum_plus90_weiblich,
         `cum_80-89` = `cum_80-89_weiblich` + `cum_80-89_maennlich`,
         `cum_70-79` = `cum_70-79_weiblich` + `cum_70-79_maennlich`,
         `cum_60-69` = `cum_60-69_weiblich` + `cum_60-69_maennlich`,
         `cum_50-59` = `cum_50-59_weiblich` + `cum_50-59_maennlich`,
         `cum_40-49` = `cum_40-49_weiblich`+ `cum_40-49_maennlich`,
         `cum_30-39` = `cum_30-39_weiblich`+`cum_30-39_maennlich`,
         `cum_20-29` = `cum_20-29_weiblich`+`cum_20-29_maennlich`,
         `cum_15-19` = `cum_15-19_weiblich`+`cum_15-19_maennlich`,
         `cum_6-14` = `cum_6-14_weiblich`+`cum_6-14_maennlich`,
         `cum_bis5` = `cum_bis5_weiblich`+`cum_bis5_maennlich`,
         `bis5` = `cum_bis5` - lag(`cum_bis5`),
         `6-14` = `cum_6-14` - lag(`cum_6-14`),
         `15-19` = `cum_15-19` - lag(`cum_15-19`),
         `20-29` = `cum_20-29` - lag(`cum_20-29`),
         `30-39` = `cum_30-39` - lag(`cum_30-39`),
         `40-49` = `cum_40-49` - lag(`cum_40-49`),
         `50-59` = `cum_50-59` - lag(`cum_50-59`),
         `60-69` = `cum_60-69` - lag(`cum_60-69`),
         `70-79` = `cum_70-79` - lag(`cum_70-79`),
         `80-89` = `cum_80-89` - lag(`cum_80-89`),
         plus90 = cum_plus90 - lag(cum_plus90),
         kw_neu = ifelse(kw == 1, 53, kw - 1),
         jahr_neu = ifelse(kw_neu == 53, 2020, jahr))%>%
  select(kw_neu, jahr_neu, cum_plus90_weiblich,cum_plus90_maennlich,`cum_80-89_weiblich`,`cum_80-89_maennlich`,`cum_70-79_weiblich`,
         `cum_70-79_maennlich`,`cum_60-69_weiblich`,`cum_60-69_maennlich`,`cum_50-59_weiblich`,`cum_50-59_maennlich`,`cum_40-49_weiblich`,
         `cum_40-49_maennlich`,`cum_30-39_weiblich`,`cum_30-39_maennlich`,`cum_20-29_weiblich`,`cum_20-29_maennlich`,`cum_15-19_weiblich`,
         `cum_15-19_maennlich`,`cum_6-14_weiblich`,`cum_6-14_maennlich`,`cum_bis5_weiblich`,`cum_bis5_maennlich`,cum_plus90,`cum_80-89`,
         `cum_70-79`,`cum_60-69`,`cum_50-59`,`cum_40-49`,`cum_30-39`,`cum_20-29`,`cum_15-19`,`cum_6-14`,`cum_bis5`, `bis5`,
         `6-14`,`15-19`,`20-29`,`30-39`,`40-49`,`50-59`,`60-69`,`70-79`,`80-89`,plus90)%>%
  rename(kw = kw_neu,
         jahr = jahr_neu)

write.csv2(data.01, "c:\\tmp\\18.csv")
