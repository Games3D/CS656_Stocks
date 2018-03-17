package utils;


import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;

public class CurrencyConverter {
	
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
