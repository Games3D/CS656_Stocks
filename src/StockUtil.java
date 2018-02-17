

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.ResultSet;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;


/**
 * Javelet used to get stock information from the web
 * 
 * @author Jared
 *
 */

@SuppressWarnings("serial")
public class StockUtil extends HttpServlet {

	enum OPCODE{STOCK_PRICE, STOCK_STANDARDPRICE};
	
	public void init() throws ServletException {
	   }

	public void doGet ( HttpServletRequest request, HttpServletResponse response ) throws ServletException, IOException    {
		OPCODE OP=OPCODE.valueOf(request.getParameter("OPCODE"));
		String PARAMS=request.getParameter("PARAMS");


		// Set response content type
		response.setContentType("text/html");

		// Actual logic goes here.
		PrintWriter out = response.getWriter();
		out.println("<h1>" + GodSwitch(OP,PARAMS) + "</h1>");
		out.println("<br><h2>DONE</h2>");
	}



	@SuppressWarnings("unused")
	private static String GodSwitch(OPCODE op, String Param) {
		String[] P=Param.split(",");
		if (P.length<=0){
			System.out.println("BAD PARAM");
			return "";
		}

		String response="";

		System.out.println("parameter OPCODE=" + op+"\nparameter PARAMS=" + Param);
		ResultSet rs=null;

		switch(op) {
		case STOCK_PRICE:
			response =  String.valueOf(new Stock("GOOGL").getPrice());
			break;
		case STOCK_STANDARDPRICE:
			response =  String.valueOf(new Stock("GOOGL").getAdjustedPrice());
			break;
		default:
			System.out.println("BAD PARAM");
			break;
		}

		System.out.println(response);
		return response;
	}
	
	public void destroy() {
	      // do nothing.
	   }
}
