using System;
using System.IO;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Azure.WebJobs;
using Microsoft.Azure.WebJobs.Extensions.Http;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Newtonsoft.Json;
// Using directives should be appended here

namespace Test2
{
    public static class Function1
    {
        // Code for custom class goes here
        [FunctionName("Function1")]
        public static async Task<IActionResult> Run(
            [HttpTrigger(AuthorizationLevel.Function, "get", "post", Route = null)] HttpRequest reqUsers,
            ILogger logUsers)
        {
            //Place your code for all items related to the HTTP requests here
        }
    }
}

//Place your answer to the bonuse below between the comments area

/*
ANSWER TO BONUS HERE
 
*/
