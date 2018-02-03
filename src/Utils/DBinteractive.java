package Utils;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Properties;

public class DBinteractive {

	public Connection conn=null;
	private StringBuilder ERRORS=new StringBuilder();

	public DBinteractive() {
		final String host="";
		final String username="";
		final String password="";

		//TODO change IBM connection string to MySQL 
		final String DRIVER = "com.ibm.as400.access.AS400JDBCDriver"; 
		final String URL = "jdbc:as400://"+host+";naming=system";//jdbc:db2

		try {
			Class.forName(DRIVER); //making the connection
			conn = DriverManager.getConnection(URL, username, password); 
		} catch (ClassNotFoundException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		} catch (SQLException e) {
			ERRORS.append("*Error creating alias: "+e.getMessage()+"\n");
			e.printStackTrace();
		}
	}

	/**
	 * closes the DB connection safely
	 */
	public void Disconnect() {
		try {
			conn.close();
		} catch (SQLException e) {
			e.printStackTrace();
			ERRORS.append("*"+e.getMessage()+"\n");
		}
	}

	/**
	 * Returns any errors which are found
	 * 
	 * @return error string
	 */
	public String GetErrors() {return ERRORS.toString();}

	/**
	 * Queries the DB using the giving query string, only returns a string not result set
	 * 
	 * @param query Query string such as "Select *"
	 * @return first search result string
	 * @exception SQLException
	 */
	@SuppressWarnings({ "unchecked", "rawtypes" })
	public List executeQuery(String sql) 
	{
		List result = new ArrayList();

		Statement statement = null;
		ResultSet resultSet = null;

		try
		{
			statement = conn.createStatement();
			resultSet = statement.executeQuery(sql);
			ResultSetMetaData metaData = resultSet.getMetaData();

			int numColumns = metaData.getColumnCount();
			while (resultSet.next())
			{
				Map row = new LinkedHashMap();
				for (int i = 0; i < numColumns; ++i)
				{
					String columnName = metaData.getColumnName(i+1);
					row.put(columnName, resultSet.getObject(i+1));
				}

				result.add(row);
			}
		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}
		finally{
			try {
				resultSet.close();
				statement.close();
			} catch (SQLException e) {
				ERRORS.append("*"+e.getMessage()+"\n");
				e.printStackTrace();
			}
		}

		return result;
	}

	/**
	 * Queries the DB using the giving query string, only returns a string not result set
	 * 
	 * @param query Query string such as "Select *"
	 * @return first search result string
	 */
	public String DBquery(String query){
		String result="";
		try {
			Statement stmt1 = conn.createStatement();
			ResultSet rs = stmt1.executeQuery(query);

			rs.next();
			result = rs.getString(1);

			rs.close();		
			stmt1.close();
		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}
		return result;
	}

	/**
	 * Queries the DB using the giving query string
	 * 
	 * @param query Query string such as "insert *" or "update *"
	 */
	public int DBupdate(String query){	
		Statement stmt = null;
		int r=-1;

		try {
			stmt = conn.createStatement();
			r=stmt.executeUpdate(query);
			stmt.close();
		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}

		return r;
	}

	/**
	 * Queries the DB using the giving query string, only returns a int not result set
	 * 
	 * @param query Query string such as "Select *"
	 * @return first search result integer
	 */
	public Integer DBqueryINT(String query){
		int result=-1;
		try {
			Statement stmt1 = conn.createStatement();
			ResultSet rs = stmt1.executeQuery(query);

			rs.next();
			result = rs.getInt(1);

			rs.close();		
			stmt1.close();
		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}
		return result;
	}

	/**
	 * Queries the DB using the giving query string, only returns a int not result set
	 * 
	 * @param query Query string such as "Select *"
	 * @return first search result integer
	 */
	public double DBqueryDOUBLE(String query){
		double result=-1;
		try {
			Statement stmt1 = conn.createStatement();
			ResultSet rs = stmt1.executeQuery(query);

			rs.next();
			result = rs.getDouble(1);

			rs.close();		
			stmt1.close();
		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}
		return result;
	}

	/**
	 * Queries the DB using the giving query string, only returns a result set
	 * 
	 * @param query Query string such as "Select *"
	 * @param rs a blank result set to be filled with data
	 * @return the result set now has data and will be returned
	 */
	public ResultSet DBqueryRS(String query, ResultSet rs){
		try {
			Statement stmt1 = conn.createStatement();
			rs = stmt1.executeQuery(query);

		} catch (SQLException e) {
			ERRORS.append("*"+e.getMessage()+"\n");
			e.printStackTrace();
		}
		return rs;
	}
}
