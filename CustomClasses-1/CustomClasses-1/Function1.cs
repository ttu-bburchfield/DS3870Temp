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

namespace CustomClasses_1
{
    

    public static class Function1
    {
        private class Athlete
        {
            public string FirstName { get; set; }
            public string LastName { get; set; }
            public int JerseyNumber { get; set; }
            public DateTime DateOfBirth { get; set; }
            public Classification Classification { get; set; }
            public Athlete(string fName, string lName, int jerseyNum, DateTime DOB, Classification clsClass)
            {
                FirstName = fName;
                LastName = lName;
                JerseyNumber = jerseyNum;
                DateOfBirth = DOB;
                Classification = clsClass;
            }
        }
        
        private class Classification
        {
            public string Description { get; set; }
            public int MinHours { get; set; }
            public Classification(string strDescription, int intMinHours)
            {
                Description = strDescription;
                MinHours = intMinHours;
            }
        }

        private class Vehicle
        {
            public string Make { get; set; }
            public string Model { get; set; }
            public int Year { get; set; }
            public int MPG { get; set; }
            public Vehicle(string strMake, string strModel, int intYear, int intMPG)
            {
                Make = strMake;
                Model = strModel;
                Year = intYear;
                MPG = intMPG;
            }
            public double CalculateFuelCost(int intMilesTravelled, double dblPricePerGallon)
            {
                return (intMilesTravelled / MPG) * dblPricePerGallon;
            }
        }


            private class SportsTeam
        {
            public string Name { get; set; }
            public string Season { get; set; }
            public int TotalPlayers { get; set; }
            public int TravelPlayers { get; set; }
            public List<Athlete> Players { get; set; }
            public int PlayersNotTravel { get; set; }
            public SportsTeam(string strName, string strSeason, int totPlayers, int travPlayers, List<Athlete> lstAthletes)
            {
                Name = strName;
                Season = strSeason;
                TotalPlayers = totPlayers;
                TravelPlayers = travPlayers;
                Players = lstAthletes;
                PlayersNotTravel = totPlayers - travPlayers;
            }
            public int CalculatePlayersCannotTravel()
            {
                return TotalPlayers - TravelPlayers;
            }
        }

        


        [FunctionName("GetCostToTravel")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request.");



            Classification clsFreshmen = new Classification("Freshman", 0);
            Classification clsSophomore = new Classification("Sophomore", 30);

            Athlete athJane = new Athlete("Jane", "Doeling", 45, System.DateTime.Now, clsFreshmen);
            Athlete athJohn = new Athlete("John", "Doe", 44, System.DateTime.Now, clsSophomore);
            List<Athlete> lstAthletes = new List<Athlete>();
            lstAthletes.Add(athJohn);
            lstAthletes.Add(athJane);
            SportsTeam sprtBasketball = new SportsTeam("Basketball", "Winter", 32, 21, lstAthletes);

            Vehicle Prius = new Vehicle("Toyota", "Prius", 2010, 49);
            Vehicle Jetta = new Vehicle("VW", "Jetta", 2013, 42);
            Vehicle Beetle = new Vehicle("VW", "Beetle", 2006, 32);
            Vehicle Challenger = new Vehicle("Dodge", "Challenger", 2013, 21);
            Vehicle Tundra = new Vehicle("Toyota", "Tundra", 2000, 12);

            List<Vehicle> lstVehicles = new List<Vehicle>();
            lstVehicles.Add(Prius);
            lstVehicles.Add(Jetta);
            lstVehicles.Add(Beetle);
            lstVehicles.Add(Challenger);
            lstVehicles.Add(Tundra);

            string strVehicleMake = req.Query["strVehicleMake"];
            string strVehicleModel = req.Query["strVehicleModel"];
            int intVehicleYear = int.Parse(req.Query["intVehicleYear"]);
            int intMilesToGo = int.Parse(req.Query["intMiles"]);
            double dblCostPerGallon = double.Parse(req.Query["dblCostOfFuel"]);

            foreach(Vehicle vehCurrent in lstVehicles)
            {
                if(vehCurrent.Make == strVehicleMake && vehCurrent.Model == strVehicleModel && vehCurrent.Year == intVehicleYear)
                {
                    return new OkObjectResult(vehCurrent.CalculateFuelCost(intMilesToGo, dblCostPerGallon));
                }
            }

            return new OkObjectResult("Vehicle Not Found");
            
        }
    }
}
