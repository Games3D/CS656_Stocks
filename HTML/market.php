<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="CSS/home.css">
<title>Untitled Document</title>
</head>

<body>
<h1>Market Page</h1>
<ul>
  <li><a href="home.php">Home</a></li>
  <li><a href="portfolio.php">Portfolio</a></li>
	<li><a href="#Stock">Stock Screener</a></li>
	<li><a href="news.php">News</a></li>
	<li><a href="market.php">Market</a></li>
	<li><div class="search-container">
    <form action="/action_page.php">
      <input type="text" placeholder="Search.." name="search">
      <button type="submit">Submit</button>
    </form>
  </div></li>
</ul>
<!-- Currency Converter Script - EXCHANGERATEWIDGET.COM -->
<br>
<br>
<div class="row">
<div style="width:198px;border:1px solid #2D6AB4;">
<div style="text-align:center;background-color:#2D6AB4;width:100%;font-size:13px;font-weight:bold;height:18px;padding-top:2px;">
<a href="https://www.exchangeratewidget.com/" style="color:#FFFFFF;text-decoration:none;" rel="nofollow">Currency Converter</a></div>
<script type="text/javascript" src="//www.exchangeratewidget.com/converter.php?l=en&f=USD&t=EUR&a=1&d=F0F0F0&n=FFFFFF&o=000000&v=1"></script></div>
<script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
<script type="text/javascript">
new TradingView.widget({
  "width": 680,
  "height": 480,
  "symbol": "NASDAQ:AAPL",
  "interval": "D",
  "timezone": "America/New_York",
  "theme": "Light",
  "style": "1",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "allow_symbol_change": true,
  "hotlist": true,
  "calendar": true,
  "hideideas": true
});
</script>
<!-- TradingView Widget END -->
	</div>


<iframe src="https://feed.mikle.com/widget/v2/64655/" height="402px" align="right" width="30%" class="fw-iframe" scrolling="no" frameborder="0"></iframe> 

<!-- TradingView Widget BEGIN -->



<!-- End of Currency Converter Script -->

</div>
</body>
</html>