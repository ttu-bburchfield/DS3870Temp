using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Azure.WebJobs;
using Microsoft.Azure.WebJobs.Extensions.Http;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Newtonsoft.Json;

namespace TaskSession
{
    public static class Function1
    {
        [FunctionName("Sessions")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", "post", Route = null)] HttpRequest req,
            ILogger log)
        {
            log.LogInformation("C# HTTP trigger function processed a request for Sessions.");
            if(req.Method == HttpMethods.Get)
            {
                //Return Session based on SessionID
                log.LogInformation("GET Request for Sessions");
            }

            if(req.Method == HttpMethods.Post)
            {
                // Create New Session
                // Need SessionID, UserID, StartDateTime, LastUsedDateTime
                log.LogInformation("POST Request for Sessions");
                string strEmail = req.Query["strEmail"];
                string strPassword = req.Query["strPassword"];

                if(strEmail == null || strPassword == null)
                {
                    return new OkObjectResult("You must provide a username and password");
                }

                INSERT INTO tblSessions VALUES (SessionID, Email, CurrentDateTime, CurrentDateTime) WHERE (SELECT COUNT(*) FROM tblUSERS WHERE TOUPPER(Email) = TOUPPER(strEmail) AND Password = strPassword))
            }

            string name = req.Query["name"];

            string requestBody = await new StreamReader(req.Body).ReadToEndAsync();
            dynamic data = JsonConvert.DeserializeObject(requestBody);
            name = name ?? data?.name;

            string responseMessage = string.IsNullOrEmpty(name)
                ? "This HTTP triggered function executed successfully. Pass a name in the query string or in the request body for a personalized response."
                : $"Hello, {name}. This HTTP triggered function executed successfully.";

            return new OkObjectResult(responseMessage);
        }
    }
}
