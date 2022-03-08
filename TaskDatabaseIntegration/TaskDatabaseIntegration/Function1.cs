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
            string strEmail = req.Query["strEmail"];
            if(strEmail == null)
            {
                return new OkObjectResult("Please Enter Email");
            }
            string strQuery = "SELECT Email, FirstName, LastName, Password FROM tblUsers";
            SqliteConnection conTasks = new SqliteConnection(@"Data Source=Data\Tasks.db");
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
                    conTasks.Close();
                    return new OkObjectResult("Email Not Found");
                } else
                {
                    conTasks.Close();
                    return new OkObjectResult("No Users In Database");
                }
                
            } catch(Exception ex)
            {
                conTasks.Close();
                log.LogInformation("Error: " + ex.Message);
                return new OkObjectResult(ex.Message.ToString());
            }
        }
    }
}
