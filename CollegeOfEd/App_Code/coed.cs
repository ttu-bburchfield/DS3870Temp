using System;
using System.Data;
using System.Data.SqlClient;
using System.Web;
using System.Web.Services;
using System.Web.Configuration;
using Newtonsoft.Json;


public class coed : System.Web.Services.WebService
{
    public string strConnection = @"Server=localhost\myInstanceName;Database=myDataBase;User Id=myUsername;Password=myPassword;";


    public DataSet findScoresByTNumber(string strTNumber)
    {
        string strQuery = "SELECT TestScores FROM dbo.tblTestScores WHERE TNumber = @TNumber";
        DataSet dsTestScores = new DataSet();
        try
        {
            using (SqlConnection conCOED = new SqlConnection(strConnection))
            using (SqlCommand comCOED = new SqlCommand(strQuery, conCOED))
            {
                var parTNumber = new SqlParameter("TNumber", SqlDbType.VarChar);
                parTNumber.Value = strTNumber;
                comCOED.Parameters.Add(parTNumber);

                SqlDataAdapter daCOED = new SqlDataAdapter(comCOED);
                daCOED.Fill(dsTestScores);

                return dsTestScores;
            }
        } catch (Exception ex)
        {
            //ex.Message.ToString();
            return dsTestScores;
        }
    }

    [WebMethod]
    public string getTestScoresByTNumber(string strTNumber)
    {
        return JsonConvert.SerializeObject(findScoresByTNumber(strTNumber).Tables["Table"], Newtonsoft.Json.Formatting.Indented);
    }
}