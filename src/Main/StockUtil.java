package Main;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.Map;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.lang.ArrayUtils;
import org.apache.commons.lang.StringUtils;

/**
 * Javelet used to get stock infomation from the web
 * 
 * @author Jared
 *
 */

@SuppressWarnings("serial")
public class StockUtil extends HttpServlet {

	enum OPCODE{TEST, STOCK_PRICE, STOCK_BUY, STOCK_SELL, UPLOAD, DOWNLOAD};
	//GETSTOCK_PRICE in: ticker, date    out: currentcy, amount
	//STOCK_BUY in:ticker, number of shares     out:return status
	//STOCK_SELL in:ticker, number of shares     out:return status

	public void doGet ( HttpServletRequest request, HttpServletResponse response ) throws ServletException, IOException    {

		   	Map<String, String[]> params = getQueryParameters(request); 
		   	
			/*if (params.size() == 2) {					
				if (contains(params.get("OPCODE"))) {
					try {
						response = GodSwitch(OPCODE.valueOf(params.get("OPCODE")),params.get("PARAMS"));
					} catch (SQLException e) {
					}

					t.sendResponseHeaders(200, response.length());

					OutputStream os = t.getResponseBody();
					os.write(response.getBytes());
					os.close();
				} else {
					t.sendResponseHeaders(404, 0);
					System.out.println("END Error");
					return;
				}

				//System.out.println("parameter OPCODE=" + params.get("OPCODE")+"\nparameter PARAMS=" + params.get("PARAMS"));
			} else { 
				System.out.println("FAIL... not enough get parms");
				t.sendResponseHeaders(404, 0);
			}*/

		 // Set response content type
		      response.setContentType("text/html");

		      // Actual logic goes here.
		      PrintWriter out = response.getWriter();
		      out.println("<h1>" + params + "</h1>");
		      out.println("<br><h2>DONE</h2>");
		      params.forEach((cur, g) -> out.println(""));
	}

	public static Map<String, String[]> getQueryParameters(HttpServletRequest request) {
	    Map<String, String[]> queryParameters = new HashMap<>();
	    String queryString = request.getQueryString();

	    if (StringUtils.isEmpty(queryString)) {
	        return queryParameters;
	    }

	    String[] parameters = queryString.split("&");

	    for (String parameter : parameters) {
	        String[] keyValuePair = parameter.split("=");
	        String[] values = queryParameters.get(keyValuePair[0]);
	        values = (String[]) ArrayUtils.add(values, keyValuePair.length == 1 ? "" : keyValuePair[1]); //length is one if no value is available.
	        queryParameters.put(keyValuePair[0], values);
	    }
	    return queryParameters;
	}

	@SuppressWarnings("unused")
	private static String GodSwitch(OPCODE op, String Param) throws SQLException {
		String[] P=Param.split(",");
		if (P.length<=0){
			System.out.println("BAD PARAM");
			return "";
		}

		String response="";

		System.out.println("parameter OPCODE=" + op+"\nparameter PARAMS=" + Param);
		ResultSet rs=null;

		switch(op) {
		case TEST:
			//rs=DB.DBqueryRS("SELECT * FROM QTEMP/INSURE_FC1 WHERE ISSN='"+P[0]+"'", rs);
			//rs.next();

			//response =  "<users>"+rs.getString("IGRPNO").trim()+"</users>";

			break;
		default:
			System.out.println("BAD PARAM");
			break;
		}

		System.out.println(response);
		return response;
	}

	public static boolean contains(String test) {

		for (OPCODE c : OPCODE.values()) {
			if (c.name().equals(test)) {
				return true;
			}
		}

		return false;
	}
}
