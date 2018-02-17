

import java.io.IOException;
import java.math.BigDecimal;
import java.util.List;

import yahoofinance.YahooFinance;
import yahoofinance.histquotes.HistoricalQuote;

public class Stock {

	private yahoofinance.Stock stock = null;

	public static void main(String[] args) {
		new Stock("GOOGL");
	}

	@SuppressWarnings("unused")
	public Stock(String symbol) {

		try {
			stock = YahooFinance.get(symbol);
		} catch (IOException e) {
			//e.printStackTrace();
			System.out.println("Can't find stock: "+symbol);
		}

		BigDecimal price = stock.getQuote().getPrice();
		BigDecimal change = stock.getQuote().getChangeInPercent();
		BigDecimal peg = stock.getStats().getPeg();
		BigDecimal dividend = stock.getDividend().getAnnualYieldPercent();

		stock.print();
	}

	public String getName() {return stock.getName();}
	public String getSymbol() {return stock.getSymbol();}
	public String getCurrency() {return stock.getCurrency();}
	public float getPrice() {return stock.getQuote().getPrice().floatValue();}
	public float getAdjustedPrice() {
		float result;
		//checks to see if the currency is USD and if it isnt then it will convert it to it
		if (!this.getCurrency().equals("USD")) {
			result=stock.getQuote().getPrice().floatValue()*CurrencyConverter.conversionRate(this.getCurrency(),"USD");
		}else{
			result=stock.getQuote().getPrice().floatValue();
		}

		return result;}
	public float getDividend() {return stock.getDividend().getAnnualYieldPercent().floatValue();}
	public List<HistoricalQuote> getHistory() {
		try {
			return stock.getHistory();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}

}
