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
            public string Classification { get; set; }
            public Athlete(string fName, string lName, int jerseyNum, DateTime DOB, string strClass)
            {
                FirstName = fName;
                LastName = lName;
                JerseyNumber = jerseyNum;
                DateOfBirth = DOB;
                Classification = strClass;
            }
        }


        private class SportsTeam
        {
            public string Name { get; set; }
            public int Season { get; set; }
            public int TotalPlayers { get; set; }
            public int TravelPlayers { get; set; }
            public List<Athlete> Players { get; set; }
            public SportsTeam(string strName, int intSeason, int totPlayers, int travPlayers, List<Athlete> lstAthletes)
            {
                Name = strName;
                Season = intSeason;
                TotalPlayers = totPlayers;
                TravelPlayers = travPlayers;
                Players = lstAthletes;
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


        [FunctionName("Function1")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", "post", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request.");

            Vehicle Prius = new Vehicle("Toyota", "Prius", 2010, 49);
            

            string name = req.Query["name"];


            return new OkObjectResult(Prius.CalculateFuelCost(1200, 3.37));
        }
    }
}
