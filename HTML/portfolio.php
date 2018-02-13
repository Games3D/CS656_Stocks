<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="CSS/home.css">
<title>Portfolio Page</title>
</head>

<body>
<h1>Portfolio Page</h1>
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
<h2>Users Portfolio</h2>

<table>
  <tr>
    <th>Stock Name</th>
    <th>Stock Symbol</th>
    <th>List Price</th>
    <th>Market Cap</th>
    <th>Open Price</th>
    <th>Close Price</th>
  </tr>
  <tr>
    <td>Test Name</td>
    <td>TEST</td>
    <td>99.99</td>
    <td>10B</td>
    <td>50.00</td>
    <td>99.99</td>
  </tr>
</table>
</body>
</html>