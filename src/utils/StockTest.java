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
import java.text.DecimalFormat;
import java.text.NumberFormat;
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

	String Crumb="";
	static String Symbol="";
	HttpClient client = HttpClientBuilder.create().build();
	HttpClientContext context = HttpClientContext.create();

	public StockTest(String symbol) {
		//setup
		Symbol=symbol;

		if (symbol.contains(","))//skips the rest of this if this is for running R
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
		String url = String.format("https://query1.finance.yahoo.com/v7/finance/download/%s?period1=%s&period2=%s&interval=1d&events=history&crumb=%s", Symbol, startDate, endDate, this.Crumb);
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
		String url = String.format("https://query1.finance.yahoo.com/v7/finance/quote?symbols=%s", Symbol);
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

	public class R implements Runnable {
		private String value="";
		private String Symbol="";

		public R(String symbol) {this.Symbol=symbol;}
		public void run() {
			String rt="";
			String[] DATA=null;

			try {
				DATA=Symbol.split(",");
			} catch (Exception e1) {
				value="BAD PARAMS";
				return;
			}
			if (DATA.length<=1) {
				value="BAD PARAMS";
				return;
			}

			
			DecimalFormat df=new DecimalFormat("0.000000000");
			
			double T=Double.parseDouble(DATA[30]);
			double B=Double.parseDouble(DATA[31]);
			//r,p,b
			double R1=Double.parseDouble(DATA[0]);
			double R2=Double.parseDouble(DATA[3]);
			double R3=Double.parseDouble(DATA[6]);
			double R4=Double.parseDouble(DATA[9]);
			double R5=Double.parseDouble(DATA[12]);
			double R6=Double.parseDouble(DATA[15]);
			double R7=Double.parseDouble(DATA[18]);
			double R8=Double.parseDouble(DATA[21]);
			double R9=Double.parseDouble(DATA[24]);
			double R10=Double.parseDouble(DATA[27]);
						
			double P1=Double.parseDouble(DATA[1]);
			double P2=Double.parseDouble(DATA[4]);
			double P3=Double.parseDouble(DATA[7]);
			double P4=Double.parseDouble(DATA[10]);
			double P5=Double.parseDouble(DATA[13]);
			double P6=Double.parseDouble(DATA[16]);
			double P7=Double.parseDouble(DATA[19]);
			double P8=Double.parseDouble(DATA[22]);
			double P9=Double.parseDouble(DATA[25]);
			double P10=Double.parseDouble(DATA[28]);
			
			double B1=Double.parseDouble(DATA[2]);
			double B2=Double.parseDouble(DATA[5]);
			double B3=Double.parseDouble(DATA[8]);
			double B4=Double.parseDouble(DATA[11]);
			double B5=Double.parseDouble(DATA[14]);
			double B6=Double.parseDouble(DATA[17]);
			double B7=Double.parseDouble(DATA[20]);
			double B8=Double.parseDouble(DATA[23]);
			double B9=Double.parseDouble(DATA[26]);
			double B10=Double.parseDouble(DATA[29]);
			
			
			String OUTSTRING=
			"max: "+df.format((R1*P1)/T)+" Q1 + "+df.format((R2*P2)/T)+" Q2 + "+df.format((R3*P3)/T)+" Q3 + "+df.format((R4*P4)/T)+" Q4 + "+df.format((R5*P5)/T)+" Q5 + "+df.format((R1*P6)/T)+" Q6 + "+df.format((R7*P7)/T)+" Q7 + "+df.format((R8*P8)/T)+" Q8 + "+df.format((R9*P9)/T)+" Q9 + "+df.format((R10*P10)/T)+" Q10;"+"\n"+
			"\n"+
			df.format(P1)+" Q1 + "+df.format(P2)+" Q2 + "+df.format(P3)+" Q3 + "+df.format(P4)+" Q4 + "+df.format(P5)+" Q5 + "+df.format(P6)+" Q6 + "+df.format(P7)+" Q7 + "+df.format(P8)+" Q8 + "+df.format(P9)+" Q9 + "+df.format(P10)+" Q10 <= "+df.format(T)+";"+"\n"+
			df.format((B1*P1)/T)+" Q1 + "+df.format((B2*P2)/T)+" Q2 + "+df.format((B3*P3)/T)+" Q3 + "+df.format((B4*P4)/T)+" Q4 + "+df.format((B5*P5)/T)+" Q5 + "+df.format((B6*P6)/T)+" Q6 + "+df.format((B7*P7)/T)+" Q7 + "+df.format((B8*P8)/T)+" Q8 + "+df.format((B9*P9)/T)+" Q9 + "+df.format((B10*P10)/T)+" Q10 <= "+df.format(B)+";"+"\n"+
			df.format(P1)+" Q1 + "+df.format(P2)+" Q2 + "+df.format(P3)+" Q3 + "+df.format(P4)+" Q4 + "+df.format(P5)+" Q5 + "+df.format(P6)+" Q6 + "+df.format(P7)+" Q7 <= "+df.format(0.75*T)+";"+"\n"+
			df.format(P1)+" Q1 + "+df.format(P2)+" Q2 + "+df.format(P3)+" Q3 + "+df.format(P4)+" Q4 + "+df.format(P5)+" Q5 + "+df.format(P6)+" Q6 + "+df.format(P7)+" Q7 >= "+df.format(0.65*T)+";"+"\n"+
			df.format(P8)+" Q8 + "+df.format(P9)+" Q9 + "+df.format(P10)+" Q10 <= "+df.format(0.35*T)+";"+"\n"+
			df.format(P8)+" Q8 + "+df.format(P9)+" Q9 + "+df.format(P10)+" Q10 >= "+df.format(0.25*T)+";"+"\n"+
			/*df.format(P1)+" Q1 >= "+0.05*T+";"+"\n"+
			df.format(P1)+" Q1 <= "+0.3*T+";"+"\n"+
			df.format(P2)+" Q2 >= "+0.05*T+";"+"\n"+
			df.format(P2)+" Q2 <= "+0.3*T+";"+"\n"+
			df.format(P3)+" Q3 >= "+0.05*T+";"+"\n"+
			df.format(P3)+" Q3 <= "+0.3*T+";"+"\n"+
			df.format(P4)+" Q4 >= "+0.05*T+";"+"\n"+
			df.format(P4)+" Q4 <= "+0.3*T+";"+"\n"+
			df.format(P5)+" Q5 >= "+0.05*T+";"+"\n"+
			df.format(P5)+" Q5 <= "+0.3*T+";"+"\n"+
			df.format(P6)+" Q6 >= "+0.05*T+";"+"\n"+
			df.format(P6)+" Q6 <= "+0.3*T+";"+"\n"+
			df.format(P7)+" Q7 >= "+0.05*T+";"+"\n"+
			df.format(P7)+" Q7 <= "+0.3*T+";"+"\n"+
			df.format(P8)+" Q8 >= "+0.05*T+";"+"\n"+
			df.format(P8)+" Q8 <= "+0.3*T+";"+"\n"+
			df.format(P9)+" Q9 >= "+0.05*T+";"+"\n"+
			df.format(P9)+" Q9 <= "+0.3*T+";"+"\n"+
			df.format(P10)+" Q10 >= "+0.05*T+";"+"\n"+
			df.format(P10)+" Q10 <= "+0.3*T+";"+"\n"+*/
			"Q1>=1;"+"\n"+
			"Q2>=1;"+"\n"+
			"Q3>=1;"+"\n"+
			"Q4>=1;"+"\n"+
			"Q5>=1;"+"\n"+
			"Q6>=1;"+"\n"+
			"Q7>=1;"+"\n"+
			"Q8>=1;"+"\n"+
			"Q9>=1;"+"\n"+
			"Q10>=1;"+"\n"+
			"\n"+
			"int Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10;";
			
			//connecting to the AFS upload
			String host="afs1.njit.edu";
			String user="jp834";
			String password="Mynjit19";
			String command1="unset LD_LIBRARY_PATH; module load R-Project/3.2.4; R < /afs/cad.njit.edu/u/j/p/jp834/public_html/run.r --no-save";
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
						//System.out.print(+new String(tmp, 0, i));
						rt=new String(tmp, 0, i);
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

			rt=rt.substring(rt.indexOf("get.variables(model1)")+23,rt.length()-4);
			value=rt.substring(rt.indexOf("]")+2);
			//System.out.println(value);
		}
		public String getValue() {
			return value;
		}
	}

	public String runR() {
		R foo = new R(Symbol);
		new Thread(foo).start();
		
		try {
			Thread.sleep(15000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		
		String value = foo.getValue();
		
		if (value.equals("BAD PARAMS")) {
			return "BAD PARAMS";
		} else if (value.equals("")) {
			return "TIME OUT";
		} else {
			return value;
		}
	}

	public static void main (String[] args) {
		//StockTest c = new StockTest("zeel.ns");
		//System.out.println(c.firstBuy());
		//System.out.println(c.getQuote());
		//System.out.println(CurrencyConverter.conversionRate("CHF", "USD"));
		StockTest c2 = new StockTest("0.005045997,204.16 ,1.073132221,-0.000995944,186.13 ,1.066861448,0.003802902,46.2729 ,0.984216143,0.000751952,204.16 ,0.690125203,0.003323035,8.793088 ,1.098406147,0.004520559,10.872237 ,1.025943826,0.000442582,14.655 ,0.717279322,0.000217747,46.2729 ,0.883875360,0.006611401,8.793088 ,0.993940550,0.003667151,10.872237 ,0.743267272,4337.8558,1.2");
		System.out.println(c2.runR());

	}

}