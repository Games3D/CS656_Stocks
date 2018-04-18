package utils;
import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io. InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
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

import com.jcraft.jsch.Channel;
import com.jcraft.jsch.ChannelExec;
import com.jcraft.jsch.ChannelSftp;
import com.jcraft.jsch.JSch;
import com.jcraft.jsch.Session;


@SuppressWarnings("deprecation")
public class StockTest {

	public static class CurrencyConverter {

		private final static String USER_AGENT = "Mozilla/5.0";

		/**
		 * given 2 currencies it will ask for the current rate and return a float value
		 *  
		 * @param from from currency
		 * @param to to currency
		 * @return the conversion rate in float form
		 */
		@SuppressWarnings("unused")
		public static float conversionRate(String from, String to) {
			try {
				String url = "https://api.fixer.io/latest?base="+from;

				URL obj = new URL(url);
				HttpURLConnection con = (HttpURLConnection) obj.openConnection();

				// optional default is GET
				con.setRequestMethod("GET");

				//add request header
				con.setRequestProperty("User-Agent", USER_AGENT);

				//System.out.println("\nSending 'GET' request to URL : " + url);
				//System.out.println("Response Code : " + con.getResponseCode());

				BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
				String inputLine;
				StringBuffer response = new StringBuffer();

				while ((inputLine = in.readLine()) != null) {
					response.append(inputLine);
				}
				in.close();

				String reply=response.toString().substring(1);
				String[] rates=reply.substring(reply.indexOf("{")+1, reply.length()-2).split(",");
				String d=rates[0].substring(1, 4);
				for (String cur:rates) {
					if (cur.substring(1, 4).equals(to))
						return Float.parseFloat(cur.substring(6));
				}
			} catch (MalformedURLException e) {
				e.printStackTrace();
			} catch (ProtocolException e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace();
			}
			return -1;
		}
	}

	String Crumb="", Symbol="";
	HttpClient client = HttpClientBuilder.create().build();
	HttpClientContext context = HttpClientContext.create();

	public StockTest(String symbol) {
		//setup
		this.Symbol=symbol;
		
		if (symbol.contains("|"))//skips the rest of this if this is for running R
			return;
		
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
		long startDate=1514851200;
		long endDate=1514872800;
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
				strFileContents=strFileContents.replaceAll(",", "`");//replaces the , with `
				String[] line=strFileContents.split("`");
				String[] quote=getQuote().split("`");

				String cc=line[10];
				if(!quote[4].equals("USD")) 
					cc=String.valueOf(CurrencyConverter.conversionRate(quote[4], "USD")*Float.parseFloat(cc));

				strFileContents+="`"+cc;

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

				String cc=DATA.get("regularMarketPrice").toString();
				if(!DATA.get("currency").toString().equals("USD")) 
					cc=String.valueOf(CurrencyConverter.conversionRate((String)DATA.get("currency"), "USD")*Float.parseFloat(cc));

				out=new String(
						DATA.get("symbol")+ "`" +
								DATA.get("marketCap")+ "`" +
								DATA.get("longName")+ "`" +
								DATA.get("regularMarketOpen")+ "`" +
								DATA.get("currency")+ "`" +
								DATA.get("regularMarketPreviousClose")+ "`" +
								DATA.get("regularMarketPrice")+ "`" +
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

	public String run() {
		//getting params from the web
		String[] DATA=null;
		
		try {
			DATA=Symbol.split(",");
		} catch (Exception e1) {
			return "BAD PARAMS";
		}
		if (DATA.length<=1)
			return "BAD PARAMS";
		
		String pb=DATA[0];
				
		//making the file output string
		String OUTSTRING=String.format("max: %s a + %s b + %s c + %s d + %s e + %s f + %s g + %s h + %s i + %s j;\r\n" + 
				"\r\n" + 
				"a + b + c +  d + e + f + g + h + i + j <= 100000;\r\n" + 
				"( %s - "+pb+" ) a + ( %s - "+pb+" ) b + ( %s - "+pb+" ) c + ( %s - "+pb+" ) d + ( %s - "+pb+" ) e + ( %s - "+pb+" ) f + ( %s - "+pb+" ) g + ( %s - "+pb+" ) h + ( %s - "+pb+") i + ( %s - "+pb+" ) j <= 0",
				DATA[1], DATA[2], DATA[3], DATA[4], DATA[5], DATA[6], DATA[7], DATA[8], DATA[9], DATA[10], DATA[11], DATA[12], DATA[13], DATA[14], DATA[15], DATA[16], DATA[17], DATA[18], DATA[19], DATA[20]);
		
		//connecting to the AFS
		String host="afs1.njit.edu";
		String user="jp834";
		String password="Mynjit19";
		String command1="cat /afs/cad.njit.edu/u/j/p/jp834/public_html/TESTR.txt";//"ls -ltr";
		try{

			java.util.Properties config = new java.util.Properties(); 
			config.put("StrictHostKeyChecking", "no");
			JSch jsch = new JSch();
			Session session=jsch.getSession(user, host, 22);
			session.setPassword(password);
			session.setConfig(config);
			session.connect();
			System.out.println("Connected to AFS");

			//write file to server
			Channel channel = session.openChannel("sftp");
			channel.connect();

			System.out.println("SFTP Connection Opened\n");
			
			ChannelSftp channelSftp = (ChannelSftp) channel;
			channelSftp.cd("/tmp");
			try (OutputStream out = channelSftp.put("/afs/cad.njit.edu/u/j/p/jp834/public_html/TESTR.txt")) {
			    OutputStreamWriter writer = new OutputStreamWriter(out);
			    writer.write(OUTSTRING);
			    writer.close();
			} catch (IOException e) {
			    e.printStackTrace();
			}
			channel.disconnect();
			channelSftp.disconnect();
			System.out.println("File has been written\n");
			
			
			//send run command
			channel=session.openChannel("exec");
			((ChannelExec)channel).setCommand(command1);
			channel.setInputStream(null);
			((ChannelExec)channel).setErrStream(System.err);
			InputStream in=channel.getInputStream();
			channel.connect();
			
			System.out.println("Command Connection Opened\n---------------");
			
			byte[] tmp=new byte[1024];
			while(true){
				while(in.available()>0){
					int i=in.read(tmp, 0, 1024);
					if(i<0)break;
					System.out.print(new String(tmp, 0, i));
				}
				if(channel.isClosed()){
					System.out.println("\n---------------\nexit-status: "+channel.getExitStatus());
					break;
				}
				try{Thread.sleep(1000);}catch(Exception ee){}
			}
			channel.disconnect();
			session.disconnect();
			System.out.println("AFS disconnected");
		}catch(Exception e){
			e.printStackTrace();
		}
		
		return "";
	}

	public static void main (String[] args) {
		//StockTest c = new StockTest("zeel.ns");
		//System.out.println(c.firstBuy());
		//System.out.println(c.getQuote());
		//System.out.println(CurrencyConverter.conversionRate("CHF", "USD"));
		StockTest c = new StockTest("0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,");
		c.run();
		System.out.println("Done");
	}

}