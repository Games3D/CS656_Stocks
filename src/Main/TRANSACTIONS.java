package Main;

public class TRANSACTIONS {
	public enum OPCODE{BUY, SELL};

	private OPCODE TRANSACTION_TYPE;
	private double AMOUNT;
	private int TRANSACTION_ID;
	private String TRANSACTION_DATE;
	private double EXPECTED_RETURN;
	private String TICKER;
	private int NUMBER_SHARES;
	
	public String getTICKER() {
		return TICKER;
	}
	public void setTICKER(String tICKER) {
		TICKER = tICKER;
	}
	public int getNUMBER_SHARES() {
		return NUMBER_SHARES;
	}
	public void setNUMBER_SHARES(int nUMBER_SHARES) {
		NUMBER_SHARES = nUMBER_SHARES;
	}
	public double getEXPECTED_RETURN() {
		return EXPECTED_RETURN;
	}
	public void setEXPECTED_RETURN(double eXPECTED_RETURN) {
		EXPECTED_RETURN = eXPECTED_RETURN;
	}
	public OPCODE getTRANSACTION_TYPE() {
		return TRANSACTION_TYPE;
	}
	public void setTRANSACTION_TYPE(OPCODE tRANSACTION_TYPE) {
		TRANSACTION_TYPE = tRANSACTION_TYPE;
	}
	public double getAMOUNT() {
		return AMOUNT;
	}
	public void setAMOUNT(double aMOUNT) {
		AMOUNT = aMOUNT;
	}
	public int getTRANSACTION_ID() {
		return TRANSACTION_ID;
	}
	public void setTRANSACTION_ID(int tRANSACTION_ID) {
		TRANSACTION_ID = tRANSACTION_ID;
	}
	public String getTRANSACTION_DATE() {
		return TRANSACTION_DATE;
	}
	public void setTRANSACTION_DATE(String tRANSACTION_DATE) {
		TRANSACTION_DATE = tRANSACTION_DATE;
	}
}
