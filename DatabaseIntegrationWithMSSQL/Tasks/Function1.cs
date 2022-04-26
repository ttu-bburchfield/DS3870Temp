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
using System.Data.SqlClient;

namespace Tasks
{
    public static class Function1
    {
        [FunctionName("Function1")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get",  Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request.");
            string strTasksConnectionString = @"Server=PCLABSQL01\COB_DS2;Database=DS3870;User Id=student;Password=Mickey2020!;";
            string name = req.Query["name"];
            DataSet dsTasks = new DataSet();
            try
            {
                string strQuery = "SELECT * FROM dbo.tblUsers";

                using (SqlConnection conTasks = new SqlConnection(strTasksConnectionString))
                using (SqlCommand comTasks = new SqlCommand(strQuery, conTasks))
                {
                    SqlDataAdapter daTasks = new SqlDataAdapter(comTasks);
                    daTasks.Fill(dsTasks);
                    return new OkObjectResult(dsTasks);
                }
            } catch(Exception ex)
            {
                return new OkObjectResult(ex.Message.ToString());
            }



            return new OkObjectResult("TEST");
        }
    }
}
