using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Azure.WebJobs;
using Microsoft.Azure.WebJobs.Extensions.Http;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Newtonsoft.Json;
using System.Data;
using Microsoft.Data.Sqlite;
using System.Collections.Generic;

namespace TaskDatabaseIntegration
{
    public class User
    {
        public string Email { get; set; }
        public string FirstName { get; set; }
        public string LastName { get; set; }
        public string Password { get; set; }
        public User (string strEmail, string strFirstName, string strLastName, string strPassword)
        {
            Email = strEmail;
            FirstName = strFirstName;
            LastName = strLastName;
            Password = strPassword;
        }
    }
    public static class Function1
    {
        [FunctionName("Function1")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request.");

            List<User> lstUsers = new List<User>();
            List<object> lstObjects = new List<object>();
            DataTable dtUsers = new DataTable("Users");
            string strEmail = req.Query["strEmail"];
            string strQuery = "SELECT Email, FirstName, LastName, Password from tblUsers";
            SqliteConnection conTasks = new SqliteConnection(@"Data Source=Data\Tasks.db;");
            try
            {
                conTasks.Open();
                
                SqliteCommand comTasks = new SqliteCommand(strQuery, conTasks);
                SqliteDataReader drTasks = comTasks.ExecuteReader();
                if (drTasks.HasRows)
                {
                    while (drTasks.Read())
                    {
                        User usrTemp = new User(drTasks.GetValue(0).ToString(), drTasks.GetValue(1).ToString(), drTasks.GetValue(2).ToString(), drTasks.GetValue(3).ToString());
                        lstUsers.Add(usrTemp);
                    }
                    foreach(User usrCurrent in lstUsers)
                    {
                        if(usrCurrent.Email == strEmail)
                        {
                            return new OkObjectResult(usrCurrent);
                        }
                    }
                }
                
            }
            catch(Exception ex)
            {
                log.LogInformation("Error: " + ex.Message);
            }
            return new OkObjectResult("Test Inside");
        }
    }
}
