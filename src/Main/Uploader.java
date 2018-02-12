package Main;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Scanner;

import Utils.DBinteractive;

public class Uploader {
	
	ArrayList<TRANSACTIONS> LIST=new ArrayList<TRANSACTIONS>();
	File FILE=null;
	public enum OPCODE{UPLOAD, DOWNLOAD};

	public static void main(String[] args) {
		new Uploader("",Uploader.OPCODE.DOWNLOAD);
		
	}
	
	public Uploader(String path, OPCODE op) {
		DBinteractive DB= new DBinteractive();//DB connection
		FILE=new File(path);//sets the file
		
		/*
		 * DOWNLOAD
		 */
		if (op==OPCODE.DOWNLOAD) {			
			//Connect to DB and get the transactions for a given username and portfollio 
			ResultSet rs=null;
			rs=DB.DBqueryRS("", rs);//TODO
			
			try {
				while (rs.next()) {//builds arraylist of transactions from DB
					TRANSACTIONS t=new TRANSACTIONS();
					t.setTRANSACTION_ID(rs.getInt("ID"));
					t.setTRANSACTION_DATE(rs.getString("DATE"));
					t.setTICKER(rs.getString("TICKER"));
					t.setNUMBER_SHARES(rs.getInt("SHARES"));
					if (rs.getString("TYPE").equals("BUY"))
						t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.BUY);
					else
						t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.SELL);
					t.setAMOUNT(rs.getDouble("AMOUNT"));
					
					LIST.add(t);
				}
			} catch (SQLException e) {
				e.printStackTrace();
			}
			
			writeCSV();//writes the list to a csv file
		}
		/*
		 * UPLOAD
		 */
		else if (op==OPCODE.UPLOAD) {
			readCSV();
			
			for (TRANSACTIONS cur:LIST) {//adds the data to the final string
				TRANSACTIONS.OPCODE TYPE=cur.getTRANSACTION_TYPE();
				String TICKER=cur.getTICKER();
				int NUM=cur.getNUMBER_SHARES();
				
				DB.DBupdate("insert");//TODO 
			}
		}else {
			System.out.println("Invalid request.");
		}
		
		DB.Disconnect();
	}

	private void writeCSV() {
		try {//creates a new file to download to
			if (!FILE.exists())
				FILE.createNewFile();
		} catch (IOException e) {
			e.printStackTrace();
		}

		StringBuilder finalString = new StringBuilder();

		for (TRANSACTIONS cur:LIST) {//adds the data to the final string
			finalString.append(cur.getTRANSACTION_ID()+",");
			finalString.append(cur.getTRANSACTION_DATE()+",");
			finalString.append(cur.getTRANSACTION_TYPE()+",");
			finalString.append(cur.getTICKER()+",");
			finalString.append(cur.getNUMBER_SHARES()+",");
			finalString.append(cur.getAMOUNT());
			finalString.append("\r\n");
		}

		try {//now writes the final string to the file
			PrintWriter out = new PrintWriter(new BufferedWriter(new FileWriter(FILE)));
			out.print(finalString.toString());
			//System.out.println("WROTE OUTPUT FILE");
			out.close();
		} catch (IOException e) {
			e.printStackTrace();
		}		
	}
	private void readCSV_quotes(){
		ArrayList<String[]> lines = new ArrayList<String[]>();

		Scanner inputStream=null;
		try{
			inputStream = new Scanner(FILE);
		}catch(FileNotFoundException e){e.printStackTrace();}

		while(inputStream.hasNext()){
			String line = inputStream.nextLine();
			String[] values = line.split(",(?=([^\"]*\"[^\"]*\")*[^\"]*$)");//",", Integer.MAX_VALUE);
			
			TRANSACTIONS t=new TRANSACTIONS();
			if (values[0].equals("BUY"))
				t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.BUY);
			else
				t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.SELL);
			t.setTICKER(values[1]);
			t.setNUMBER_SHARES(Integer.parseInt(values[2]));
			
			LIST.add(t);
		}	
	}
	private void readCSV(){
		Scanner inputStream=null;
		try{
			inputStream = new Scanner(FILE);
		}catch(FileNotFoundException e){e.printStackTrace();}

		while(inputStream.hasNext()){
			String line = inputStream.nextLine();
			String[] values = line.split(",", Integer.MAX_VALUE);
			
			TRANSACTIONS t=new TRANSACTIONS();
			if (values[0].equals("BUY"))
				t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.BUY);
			else
				t.setTRANSACTION_TYPE(TRANSACTIONS.OPCODE.SELL);
			t.setTICKER(values[1]);
			t.setNUMBER_SHARES(Integer.parseInt(values[2]));
			
			LIST.add(t);
		}	
	}
}
