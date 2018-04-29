<%@ page language="java" contentType="text/html; charset=ISO-8859-1"
    pageEncoding="ISO-8859-1"%>
<%@page import="utils.*"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Our Stock Quote</title>
</head>
<body>
<%
StockTest c = new StockTest(request.getParameter("PARAMS"));
//StockTest c = new StockTest("GOOGL");

if (request.getParameter("OPCODE").equals("FIRSTBUY")){
	out.print(c.firstBuy());
}else if (request.getParameter("OPCODE").equals("GETQUOTE")){
	out.print(c.getQuote());
}else if (request.getParameter("OPCODE").equals("AFS")){
	out.print(c.runR());
}
%>
</body>
</html>