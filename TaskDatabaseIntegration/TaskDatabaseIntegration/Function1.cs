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
        [FunctionName("Users")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", "post", Route = null)] HttpRequest req,
            ILogger log)
        {
            if(req.Method == HttpMethods.Get)
            {
                log.LogInformation("C# HTTP trigger function processed a request from GET.");

                List<User> lstUsers = new List<User>();
                string strEmail = req.Query["strEmail"];
                if (strEmail == null)
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
                        foreach (User usrCurrent in lstUsers)
                        {
                            if (usrCurrent.Email == strEmail)
                            {
                                return new OkObjectResult(usrCurrent);
                            }
                        }
                        conTasks.Close();
                        return new OkObjectResult("Email Not Found");
                    }
                    else
                    {
                        conTasks.Close();
                        return new OkObjectResult("No Users In Database");
                    }

                }
                catch (Exception ex)
                {
                    conTasks.Close();
                    log.LogInformation("Error: " + ex.Message);
                    return new OkObjectResult(ex.Message.ToString());
                }
            }
            
            if(req.Method == HttpMethods.Post)
            {
                log.LogInformation("Inside the POST IF");

                string strEmail = req.Query["strEmail"];
                string strPassword = req.Query["strPassword"];
                string strFirstName = req.Query["strFirstName"];
                string strLastName = req.Query["strLastName"];

                if(strEmail == null || strPassword == null || strFirstName == null || strLastName == null)
                {
                    return new OkObjectResult("You must provide an Email, Password, First Name, and Last Name to create a user");
                }
                string strQuery = "INSERT INTO tblUsers VALUES($email, $password, $firstname, $lastname)";
                SqliteConnection conTasks = new SqliteConnection(@"Data Source=Data\Tasks.db");
                try
                {
                    conTasks.Open();
                    SqliteCommand comTasks = new SqliteCommand(strQuery, conTasks);
                    // Begin of parameters for prepared statement
                    SqliteParameter parEmail = new SqliteParameter("$email", SqliteType.Text);
                    parEmail.Value = strEmail;
                    comTasks.Parameters.Add(parEmail);

                    SqliteParameter parPassword = new SqliteParameter("$password", SqliteType.Text);
                    parPassword.Value = strPassword;
                    comTasks.Parameters.Add(parPassword);

                    SqliteParameter parFirstName = new SqliteParameter("$firstname", SqliteType.Text);
                    parFirstName.Value = strFirstName;
                    comTasks.Parameters.Add(parFirstName);

                    SqliteParameter parLastName = new SqliteParameter("$lastname", SqliteType.Text);
                    parLastName.Value = strLastName;
                    comTasks.Parameters.Add(parLastName);
                    // End of parameters for prepared statement

                    int intRows = comTasks.ExecuteNonQuery();
                    if(intRows > 0)
                    {
                        conTasks.Close();
                        return new OkObjectResult("User Added");
                    } else
                    {
                        conTasks.Close();
                        return new OkObjectResult("Error:  User Not Added");
                    }

                } catch (Exception ex)
                {
                    log.LogInformation("Error: " + ex.ToString());
                    conTasks.Close();
                    return new OkObjectResult(ex.ToString());
                }
            }

            return new OkObjectResult("Error:  You must send a GET or POST request");
        }
    }
}
