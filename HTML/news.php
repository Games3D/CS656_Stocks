<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="CSS/home.css">

<title>News Page</title>
</head>

<body>
<h1>News Page</h1>
<ul>
  <li><a href="home.php">Home</a></li>
  <li><a href="portfolio.php">Portfolio</a></li>
	<li><a href="#Stock">Stock Screener</a></li>
	<li><a href="#news">News</a></li>
	<li><a href="market.php">Market</a></li>
	<li><div class="search-container">
    <form action="/action_page.php">
      <input type="text" placeholder="Search.." name="search">
      <button type="submit">Submit</button>
    </form>
  </div></li>
</ul>
<iframe src="https://feed.mikle.com/widget/v2/64655/" height="900px" align="right" width="100%" class="fw-iframe" scrolling="no" frameborder="0"></iframe> 
</body>
</html>