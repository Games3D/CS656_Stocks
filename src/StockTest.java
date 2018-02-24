import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io. InputStreamReader;
import java.util.Arrays;
import java.util.Map;

import org.apache.commons.lang3.StringEscapeUtils;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.protocol.HttpClientContext;
import org.apache.http.client.utils.HttpClientUtils;
import org.apache.http.impl.client.BasicCookieStore;
import org.apache.http.impl.client.HttpClientBuilder;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;


@SuppressWarnings("deprecation")
public class StockTest {
	String Crumb="", Symbol="";
	HttpClient client = HttpClientBuilder.create().build();
	HttpClientContext context = HttpClientContext.create();

	public StockTest(String symbol) {
		//setup
		this.Symbol=symbol;
		this.client = HttpClientBuilder.create().build();
		this.context = HttpClientContext.create();
		this.context.setCookieStore(new BasicCookieStore());
		
		
		//gets the current crumb
		StringBuffer result = new StringBuffer();
		String line = "";
		
		String url = String.format("https://finance.yahoo.com/quote/%s/?p=%s", symbol, symbol);
		HttpGet request = new HttpGet(url);

		request.addHeader("User-Agent", "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13");
		try {
			HttpResponse response = client.execute(request, context);
			BufferedReader rd = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
			while ((line = rd.readLine()) != null) 
				result.append(line);
			
			HttpClientUtils.closeQuietly(response);
		} catch (Exception e) {e.printStackTrace();}

		for (String cur : Arrays.asList(result.toString().split("}"))) {
			if (cur.indexOf("CrumbStore") > -1) 
				this.Crumb = StringEscapeUtils.unescapeJava(cur.split(":")[2].replace("\"", "")); 
		}
	}

	public String firstBuy() {
		long startDate=1517443200;
		long endDate=1517529600;
		String strFileContents = ""; 
		
		//gets the data
		String url = String.format("https://query1.finance.yahoo.com/v7/finance/download/%s?period1=%s&period2=%s&interval=1d&events=history&crumb=%s", this.Symbol, startDate, endDate, this.Crumb);
		HttpGet request = new HttpGet(url);

		request.addHeader("User-Agent", "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13");
		try {
			HttpResponse response = client.execute(request, context);
			HttpEntity entity = response.getEntity();

			if (entity != null) {
				BufferedInputStream bis = new BufferedInputStream(entity.getContent());
				byte[] contents = new byte[1024];

				int bytesRead = 0;
				while((bytesRead = bis.read(contents)) != -1) { 
					strFileContents += new String(contents, 0, bytesRead);              
				}
				
				bis.close();
			}
			HttpClientUtils.closeQuietly(response);

		} catch (Exception e) {e.printStackTrace();}
		
		return strFileContents;
	}

	@SuppressWarnings({ "rawtypes"})
	public String getQuote() {
		String out="";
		
		//gets the data
		String url = String.format("https://query1.finance.yahoo.com/v7/finance/quote?symbols=%s", this.Symbol);
		HttpGet request = new HttpGet(url);

		request.addHeader("User-Agent", "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13");
		try {
			HttpResponse response = client.execute(request, context);
			HttpEntity entity = response.getEntity();

			if (entity != null) {
				BufferedInputStream bis = new BufferedInputStream(entity.getContent());
				byte[] contents = new byte[1024];

				int bytesRead = 0;
				String strFileContents = ""; 
				while((bytesRead = bis.read(contents)) != -1) { 
					strFileContents += new String(contents, 0, bytesRead);              
				}
		         
		        JSONObject jo = (JSONObject) new JSONParser().parse(strFileContents);
		        Map l1 = (Map) jo.get("quoteResponse");
		        JSONArray l2 = (JSONArray) l1.get("result");
		        JSONObject DATA = (JSONObject) l2.get(0);
		        	
		        String cc=(String) DATA.get("regularMarketPrice");
		        if(!((String)DATA.get("currency")).equals("USD")) 
		        	cc=String.valueOf(CurrencyConverter.conversionRate((String)DATA.get("currency"), "USD"));
		        
				out=new String(
				DATA.get("symbol")+ "," +
				DATA.get("marketCap")+ "," +
				DATA.get("longName")+ "," +
				DATA.get("regularMarketOpen")+ "," +
				DATA.get("currency")+ "," +
				DATA.get("regularMarketPreviousClose")+ "," +
				DATA.get("regularMarketPrice")+ "," +
				cc);
				
				//System.out.println(DATA.get("market"));
				//System.out.println(DATA.get("exchange"));
				//System.out.println(DATA.get("shortName"));
				//System.out.println(DATA.get("fullExchangeName"));
				//System.out.println(DATA.get("financialCurrency"));	
			}
			HttpClientUtils.closeQuietly(response);

		} catch (Exception e) {e.printStackTrace();}
		return out;
	}
	
	
	public static void main (String[] args) {
		StockTest c = new StockTest("GOOGL");
		c.firstBuy();
		//c.getQuote();
		System.out.println("Done");
	}

}