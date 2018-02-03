package Utils;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.logging.Formatter;
import java.util.logging.Handler;
import java.util.logging.Level;
import java.util.logging.LogRecord;

/**
 * This custom formatter formats parts of a log record to a single html log
 * @author Jared
 *
 */
class MyHtmlFormatter extends Formatter {
	private String callingClass = null;
	
	public MyHtmlFormatter(String cc) {this.callingClass=cc;}
    
	// this method is called for every log records
    public String format(LogRecord rec) {
        StringBuffer buf = new StringBuffer(1000);
        buf.append("<tr>\n");

        // colorize any levels >= WARNING in red
        if (rec.getLevel().intValue() == Level.SEVERE.intValue()) {
            buf.append("\t<td style=\"color:red\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else if (rec.getLevel().intValue() == Level.WARNING.intValue()) {
            buf.append("\t<td style=\"color:orange\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else if (rec.getLevel().intValue() == Level.CONFIG.intValue()) {
            buf.append("\t<td style=\"color:lightblue\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else if (rec.getLevel().intValue() == Level.FINE.intValue()) {
            buf.append("\t<td style=\"color:lightgreen\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else if (rec.getLevel().intValue() == Level.FINER.intValue()) {
            buf.append("\t<td style=\"color:green\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else if (rec.getLevel().intValue() == Level.FINEST.intValue()) {
            buf.append("\t<td style=\"color:darkgreen\">");
            buf.append("<b>");
            buf.append(rec.getLevel());
            buf.append("</b>");
        } else {
            buf.append("\t<td>");
            buf.append(rec.getLevel());
        }

        buf.append("</td>\n");
        buf.append("\t<td>");
        buf.append(rec.getSourceMethodName());
        buf.append("</td>\n");
        buf.append("\t<td>");
        buf.append(calcDate(rec.getMillis()));
        buf.append("</td>\n");
        buf.append("\t<td>");
        buf.append(formatMessage(rec));
        buf.append("</td>\n");
        buf.append("\t<td>");
        buf.append(rec.getThrown());
        buf.append("</td>\n");
        buf.append("</tr>\n");

        return buf.toString();
    }

    private String calcDate(long millisecs) {
        SimpleDateFormat date_format = new SimpleDateFormat("MMM dd,yyyy HH:mm:ss");
        Date resultdate = new Date(millisecs);
        return date_format.format(resultdate);
    }

    // this method is called just after the handler using this
    // formatter is created
    public String getHead(Handler h) {
        return "<!DOCTYPE html>\n<head>\n<style>\n"
            + "table { width: 100% }\n"
            + "th { font:bold 10pt Tahoma; }\n"
            + "td { font:normal 10pt Tahoma; }\n"
            + "h1 {font:normal 11pt Tahoma;}\n"
            + "</style>\n"
            + "</head>\n"
            + "<body><hr>\n"
            + "<h2>" + callingClass + "</h2>"
            + "<h1>" + (new Date()) + "</h1>"
            + "<table border=\"0\" cellpadding=\"5\" cellspacing=\"3\">\n"
            + "<tr align=\"left\">\n"
            + "\t<th style=\"width:5%\">Loglevel</th>\n"
            + "\t<th style=\"width:10%\">Calling Method</th>\n"
            + "\t<th style=\"width:15%\">Time</th>\n"
            + "\t<th style=\"width:45%\">Log Message</th>\n"
            + "\t<th style=\"width:25%\">Thrown Expection</th>\n"

            + "</tr>\n";
      }

    // this method is called just after the handler using this
    // formatter is closed
    public String getTail(Handler h) {
        return "</table>\n</body>\n</html>";
    }
}