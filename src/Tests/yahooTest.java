package Tests;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.math.BigDecimal;
import java.net.URL;
import java.net.URLConnection;

import yahoofinance.Stock;
import yahoofinance.YahooFinance;

/**
 * General test class used for testing anything we need to get from Yahoo
 * 
 * NOTE: please make separate methods for anything you do in here
 * 
 * @author Jared
 */
import java.io.IOException;
import java.lang.reflect.Field;
import java.util.Calendar;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import Utils.MyLogger;
import yahoofinance.histquotes.HistQuotesRequest;
import yahoofinance.histquotes.HistoricalQuote;
import yahoofinance.histquotes.Interval;
import yahoofinance.histquotes2.HistDividendsRequest;
import yahoofinance.histquotes2.HistQuotes2Request;
import yahoofinance.histquotes2.HistSplitsRequest;
import yahoofinance.histquotes2.HistoricalDividend;
import yahoofinance.histquotes2.HistoricalSplit;
import yahoofinance.quotes.query1v7.StockQuotesQuery1V7Request;
import yahoofinance.quotes.stock.StockDividend;
import yahoofinance.quotes.stock.StockQuote;
import yahoofinance.quotes.csv.StockQuotesData;
import yahoofinance.quotes.csv.StockQuotesRequest;
import yahoofinance.quotes.stock.StockStats;

public class yahooTest {
	//https://financequotes-api.com/
	//http://blog.bradlucas.com/posts/2017-06-04-yahoo-finance-quote-download-java/
	
	
	//private static java.util.logging.Logger LOGGER = new MyLogger(yahooTest.class.getName(), "Logging.html", "Logging.txt");
	
	public static void main(String[] args) {
		//LOGGER=MyLogger.getLogger(MyLogger.GLOBAL_LOGGER_NAME);
		
		getStockQuote("INTC");
		//getStock("INTC");
	}
	
	private static void getStock(String symbol) {  
		String sym = symbol.toUpperCase();
		double price = 0.0;
		int volume = 0;
		double pe = 0.0;
		double eps = 0.0;
		double week52low = 0.0;
		double week52high = 0.0;
		double daylow = 0.0;
		double dayhigh = 0.0;
		double movingav50day = 0.0;
		double marketcap = 0.0;
		String name = "";
		String currency = "";
		double shortRatio = 0.0;
		double open = 0.0;
		double previousClose = 0.0;
		String exchange;
		try { 
			
			// Retrieve CSV File
			URL yahoo = new URL("http://finance.yahoo.com/d/quotes.csv?s="+ symbol + "&f=nab");
			//URL yahoo = new URL("http://download.finance.yahoo.com/d/quotes.csv?s="+ symbol + "&f=l1vr2ejkghm3j3nc4s7pox");
			URLConnection connection = yahoo.openConnection();
			InputStreamReader is = new InputStreamReader(connection.getInputStream());
			BufferedReader br = new BufferedReader(is);  
			
			
			System.out.println("http://finance.yahoo.com/d/quotes.csv?s="+ symbol + "&f=l1vr2ejkghm3j3nc4s7pox");
			
			
			// Parse CSV Into Array
			String line = br.readLine();
			//Only split on commas that aren't in quotes
			String[] stockinfo = line.split(",(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)");
			
			for (String cur:stockinfo)
				System.out.println(cur);
			// Handle Our Data
			/*StockHelper sh = new StockHelper();
			
			price = sh.handleDouble(stockinfo[0]);
			volume = sh.handleInt(stockinfo[1]);
			pe = sh.handleDouble(stockinfo[2]);
			eps = sh.handleDouble(stockinfo[3]);
			week52low = sh.handleDouble(stockinfo[4]);
			week52high = sh.handleDouble(stockinfo[5]);
			daylow = sh.handleDouble(stockinfo[6]);
			dayhigh = sh.handleDouble(stockinfo[7]);   
			movingav50day = sh.handleDouble(stockinfo[8]);
			marketcap = sh.handleDouble(stockinfo[9]);
			name = stockinfo[10].replace("\"", "");
			currency = stockinfo[11].replace("\"", "");
			shortRatio = sh.handleDouble(stockinfo[12]);
			previousClose = sh.handleDouble(stockinfo[13]);
			open = sh.handleDouble(stockinfo[14]);
			exchange = stockinfo[15].replace("\"", "");*/
			
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	private static void getStockQuote(String symbol) {
		Stock stock = null;
		try {
			stock = YahooFinance.get(symbol);
		} catch (IOException e) {
			e.printStackTrace();
			System.out.println("Can't find stock: "+symbol);
		}
		 
		BigDecimal price = stock.getQuote().getPrice();
		BigDecimal change = stock.getQuote().getChangeInPercent();
		BigDecimal peg = stock.getStats().getPeg();
		BigDecimal dividend = stock.getDividend().getAnnualYieldPercent();
		 
		stock.print();
	}
}
