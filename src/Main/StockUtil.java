package Main;

import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.lang.*;
import java.util.*;
import java.text.SimpleDateFormat;

/**
 * Javelet used to get stock infomation from the web
 * 
 * @author Jared
 *
 */

public class StockUtil extends HttpServlet {

        public void doGet ( HttpServletRequest request, HttpServletResponse response )
        throws ServletException, IOException    {

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
        }// end doGet

}///:~
