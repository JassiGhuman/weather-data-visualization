import pandas as pd
import numpy as np

data = pd.read_csv(r"E:\MM2020\802\A3\data\wind_speed.csv")

data.dropna(inplace=True)
data.reset_index(drop=True, inplace=True)
print(data)
shape = data.shape
row = shape[0]
col = shape[1]

column_name = ["datetime","Vancouver","Portland","San Francisco","Seattle","Los Angeles","San Diego",
               "Las Vegas","Phoenix","Albuquerque","Denver","San Antonio","Dallas","Houston","Kansas City",
               "Minneapolis","Saint Louis","Chicago","Nashville","Indianapolis","Atlanta","Detroit",
               "Jacksonville","Charlotte","Miami","Pittsburgh","Toronto","Philadelphia","New York",
               "Montreal","Boston","Beersheba","Tel Aviv District","Eilat","Haifa","Nahariyya","Jerusalem"]

date_time = []
date = data[column_name[0]]
for i in range(1,row):
    day = date[i].split(" ")[0]
    if len(date_time) == 0 or date_time[-1] != day:
        date_time.append(day)
#日期
day_data = pd.Series(date_time)
city_series = [day_data]
for i in range(1,col):
    print(column_name[i])
    date = data[column_name[0]]  #取data第一列
    city = data[column_name[i]]
    new_city = []
    j = 1
    while j != row-1:
       day = date[j].split(" ")[0].split("-")[-1]
       new_day_data = city[j]
       k = 1
       while 1 :
           day_next = date[j+k].split(" ")[0].split("-")[-1]
           if day == day_next:
               new_day_data += city[j+k]
               k = k + 1
           else:
               new_day_data = new_day_data / k
               new_city.append(new_day_data)
               j = j + k
               break

    new_city = pd.Series(new_city)
    city_series.append(new_city)


obj_dict = {}

for i in range(0,36):
    obj_dict[column_name[i]]=city_series[i]

data_per_day=pd.DataFrame(obj_dict)
data_per_day.to_csv(r"./wind_speed_per_day.csv",index=False,header=True)
print()