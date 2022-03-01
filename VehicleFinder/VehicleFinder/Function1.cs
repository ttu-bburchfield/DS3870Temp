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

namespace VehicleFinder
{
    public static class Function1
    {
        private class Vehicle
        {
            public string Make { get; set; }
            public string Model { get; set; }
            public int Year { get; set; }
            public Vehicle(string strMake, string strModel, int intYear)
            {
                Make = strMake;
                Model = strModel;
                Year = intYear;
            }
        }

        private class Classroom
        {
            public string Building { get; set; }
            public string Room { get; set; }
            public int Capacity { get; set; }
            public ClassroomType Type { get; set; }
            public Classroom(string strBuilding, string strRoom, int intCapacity, ClassroomType ctType)
            {
                Building = strBuilding;
                Room = strRoom;
                Capacity = intCapacity;
                Type = ctType;
            }
        }

        private class ClassroomType
        {
            public string Description { get; set; }
            public bool TechnologyExists { get; set; }
            public bool SpecialUse { get; set; }
            public ClassroomType(string strDescription, bool blnTechnologyExists, bool blnSpecialUse)
            {
                Description = strDescription;
                TechnologyExists = blnTechnologyExists;
                SpecialUse = blnSpecialUse;
            }
        }

        private class User
        {
            public string FirstName { get; set; }
            public string LastName { get; set; }
            public string MiddleInit { get; set; }
            public string Email { get; set; }
            public DateTime DateOfBirth { get; set; }
            public User(string strFirstName, string strLastName, string strMiddleInit, string strEmail, DateTime datDOB)
            {
                FirstName = strFirstName;
                LastName = strLastName;
                MiddleInit = strMiddleInit;
                Email = strEmail;
                DateOfBirth = datDOB;
            }
        }

        [FunctionName("Function1")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request.");
            ClassroomType ctLecture = new ClassroomType("Lecture", true, false);
            ClassroomType ctLab = new ClassroomType("Lab", false, true);

            Classroom JH307 = new Classroom("Johnson Hall", "307", 37, ctLecture);


            Vehicle vehPrius = new Vehicle("Toyota", "Prius", 2010);
            Vehicle vehTundra = new Vehicle("Toyota", "Tundra", 2000);
            List<Vehicle> lstVehicles = new List<Vehicle>();
            lstVehicles.Add(vehPrius);
            lstVehicles.Add(vehTundra);

            User usrTemp = new User("Ben", "Burchfield", "N", "bburchfield@tntech.edu", DateTime.Parse("2022-03-01"));
            

            string strVehicleModel = req.Query["strVehicleModel"];

            List<Vehicle> lstFound = new List<Vehicle>();
            foreach(Vehicle currentVehicle in lstVehicles)
            {
                if(currentVehicle.Model == strVehicleModel)
                {
                    lstFound.Add(currentVehicle);
                }
            }

            if(lstFound.Count > 0)
            {
                return new OkObjectResult(lstFound);
            } else
            {
                return new OkObjectResult("No Vehicles Found");
            }
            
            
        }
    }
}
