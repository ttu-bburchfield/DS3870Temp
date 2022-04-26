using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Azure.WebJobs;
using Microsoft.Azure.WebJobs.Extensions.Http;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Newtonsoft.Json;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Data;

namespace Test2ReviewSpring2022
{
    public static class Function1
    {
        private class Vehicle
        {
            public string Make { get; set; }
            public string Model { get; set; }
            public string Year { get; set; }
            public Vehicle(string strMake, string strModel, string strYear)
            {
                Make = strMake;
                Model = strModel;
                Year = strYear;
            }
            public int YearsOld()
            {
                DateTime datCurrent = System.DateTime.Now;
                int intYear = datCurrent.Year;
                return intYear - int.Parse(Year);
            }
        }

        [FunctionName("Vehicles")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", "post", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("Vehicle Function Called");

            Vehicle vehPrius = new Vehicle("Toyota", "Prius", "2010");
            Vehicle vehTundra = new Vehicle("Toyota", "Tundra", "2000");
            Vehicle vehTundra2 = new Vehicle("Toyota", "Tundra", "2002");

            List<Vehicle> lstVehicles = new List<Vehicle>();
            lstVehicles.Add(vehPrius);
            lstVehicles.Add(vehTundra);
            lstVehicles.Add(vehTundra2);

            log.LogInformation("Prius is " + vehPrius.YearsOld() + " years old");

            string strMake = req.Query["Make"];
            string strModel = req.Query["Model"];
            List<Vehicle> lstFound = new List<Vehicle>();

            foreach(Vehicle vehCurrent in lstVehicles)
            {
                if(vehCurrent.Make == strMake && vehCurrent.Model == strModel)
                {
                    lstFound.Add(vehCurrent);
                }
            }

            string strEmail = req.Query["Email"];
            string strStatus = req.Query["Status"];

            DataSet dsUsers = new DataSet();
            string strConnection = @"Server=PCLABSQL01\COB_DS2;Database=DS3870;User Id=student;Password=Mickey2020!;";
            string strQuery = "SELECT * FROM dbo.tblUsers WHERE Email = @EmailAddress AND Status = @Status";

            using(SqlConnection conUsers = new SqlConnection(strConnection))
            using(SqlCommand comUsers = new SqlCommand(strQuery, conUsers))
            {
                SqlParameter parEmail = new SqlParameter("EmailAddress", SqlDbType.VarChar);
                parEmail.Value = strEmail;
                comUsers.Parameters.Add(parEmail);

                SqlParameter parStatus = new SqlParameter("Status", SqlDbType.VarChar);
                parStatus.Value = strStatus;
                comUsers.Parameters.Add(parStatus);

                SqlDataAdapter daUsers = new SqlDataAdapter(comUsers);
                daUsers.Fill(dsUsers);
                return new OkObjectResult(dsUsers.Tables[0]);
            }

            if (lstFound.Count < 1)
            {
                return new OkObjectResult("Vehicle Not Found");
            }
            return new OkObjectResult(lstFound);
        }
    }
}
