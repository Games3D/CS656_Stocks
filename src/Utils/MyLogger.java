package Utils;

import java.io.IOException;
import java.util.logging.FileHandler;
import java.util.logging.Formatter;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.logging.SimpleFormatter;

/**
 * Creates either a html, txt, or both log of any events passed to it
 * 
 * @author Jared
 *
 */
public class MyLogger extends Logger{
	private FileHandler fileTxt;
	private SimpleFormatter formatterTxt;

	private FileHandler fileHTML;
	private Formatter formatterHTML;

	public MyLogger(String bundle, String HTMLPATH, String TXTPATH) {
		super(bundle, null);

		try {
			// get the global logger to configure it
			Logger logger = Logger.getLogger(Logger.GLOBAL_LOGGER_NAME);

			// suppress the logging output to the console
			/*Logger rootLogger = Logger.getLogger("");
			Handler[] handlers = rootLogger.getHandlers();
			if (handlers[0] instanceof ConsoleHandler) {
				rootLogger.removeHandler(handlers[0]);
			}*/

			logger.setLevel(Level.FINEST);			

			// create a TXT formatter
			if (TXTPATH!=null) {
				fileTxt = new FileHandler(TXTPATH, true);
				formatterTxt = new SimpleFormatter();
				fileTxt.setFormatter(formatterTxt);
				logger.addHandler(fileTxt);
			}

			// create an HTML formatter
			if (HTMLPATH!=null) {
				fileHTML = new FileHandler(HTMLPATH, true);
				formatterHTML = new MyHtmlFormatter(bundle);
				fileHTML.setFormatter(formatterHTML);
				logger.addHandler(fileHTML);
			}
		} catch (IOException e) {
			e.printStackTrace();
			throw new RuntimeException("Problems with creating the log files");
		}
	}

}