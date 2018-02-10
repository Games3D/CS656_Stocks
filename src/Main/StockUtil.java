package Main;

import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.*;
import java.text.SimpleDateFormat;

/**
 * Javelet used to get stock infomation from the web
 * 
 * @author Jared
 *
 */

public class StockUtil extends HttpServlet {
	
	enum OPCODE{TEST, STOCK_PRICE, STOCK_BUY, STOCK_SELL, UPLOAD, DOWNLOAD};
	//GETSTOCK_PRICE in: ticker, date    out: currentcy, amount
	//STOCK_BUY in:ticker, number of shares     out:return status
	//STOCK_SELL in:ticker, number of shares     out:return status

        public void doGet ( HttpServletRequest request, HttpServletResponse response ) throws ServletException, IOException    {

    /*    	Map<String, String> params = queryToMap(request.getRequestURI().getQuery()); 
			if (params.size() == 2) {					
				if (contains(params.get("OPCODE"))) {
					String response = null;
					try {
						response = GodSwitch(OPCODE.valueOf(params.get("OPCODE")),params.get("PARAMS"));
					} catch (SQLException e) {
						//e.printStackTrace();
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
        	
                response.setContentType("text/html");
                PrintWriter out = response.getWriter();
                String serverInfo = getServletContext().getServerInfo();
                
                SimpleDateFormat sdf=null;
                File file=null;
               try{ 
               file = new File("/afs/cad.njit.edu/u/c/m/jp834/public_html/tomcat8/WEB-INF/classes/HelloWorld.java");
                sdf = new SimpleDateFormat("MM-dd-yyyy HH:mm");
               }catch (Exception e){}
                
                
    out.println("<html>");
    out.println("<head><title>Servlet Sample Program</title></head>");
    out.println("<body>");
		out.println("<h1 align=\"Center\">");
		out.println("Group Members:");
		out.println("</h1>");
		out.println("<hr>");
		out.println("<h2 align=\"Center\">");
		out.println("<font color=\"#AA0000\">");
    out.println("Chris (Leader)");
		out.println("Jared");
    out.println("Pedro");
    out.println("Nedha");
    out.println("Saladin");
		out.println("</font>");
		out.println("</h2>");
		out.println("<h3 align=\"Center\">");
		out.println("<font color=\"#AA0000\">Server: ");
		out.println(serverInfo);
		out.println("</font>");
		out.println("</h3>");
   out.println("<h4 align=\"Center\">");
		out.println("<font color=\"#AA0000\">Deployment Date: ");
		out.println(sdf.format(file.lastModified()));
		out.println("</font>");
		out.println("</h4>");
    out.println("<h5 align=\"Center\">");
		out.println("<font color=\"#AA0000\">Timestamp: ");
    out.println(new Date());
		out.println("</font>");
    out.println("</h5>");
    out.println("</body>");
        }

      //https://stackoverflow.com/questions/11640025/how-to-obtain-the-query-string-in-a-get-with-java-httpserver-httpexchange
    	public static Map<String, String> queryToMap(String query){

    		Map<String, String> result = new HashMap<String, String>();
    		for (String param : query.split("&")) {
    			String pair[] = param.split("=");
    			if (pair.length>1) {
    				result.put(pair[0], pair[1]);
    			}else{
    				result.put(pair[0], "");
    			}
    		}
    		return result;
    	}
        
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
