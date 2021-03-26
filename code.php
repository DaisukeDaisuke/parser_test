<?php

class code{
	//metadata
	const TYPE_BYTE = 1;
	const TYPE_SHORT = 2;
	const TYPE_INT = 4;
	const TYPE_LONG = 8;
	const TYPE_DOUBLE = 9;

	const TYPE_SIZE_DOUBLE = 8;
	//type bool

	//opcode

	const NOP = "\x00";//(string) null or nop

	//valueOP(Scalar)
	const READV = "\x91";//readv $a(0)
	const WRITEV = "\x92";//writev output int size 1
	const INT = "\x93";
	const STRING = "\x94";
	//const DOUBLE = "\xC4";
	//const READV = "\x00";

	//binaryOP
	const ADD = "\x02";//add 80 1000 $a
	const MUL = "\x03";
	const DIV = "\x04";
	const MINUS = "\x05";
	const B_AND = "\x06";
	const B_OR = "\x07";
	const B_XOR = "\x08";
	const BOOL_AND = "\x09";
	const BOOL_OR = "\x0A";
	const COALESCE = "\x0B";
	const CONCAT = "\x0C";

	const EQUAL = "\x0D";
	const GREATER = "\x0E";
	const GREATEROREQUAL = "\x0F";

	const IDENTICAL = "\x10";
	const L_AND = "\x11";
	const L_OR = "\x12";
	const L_XOR = "\x13";
	const MOD = "\x14";
	const NOTIDENTICAL = "\x15";
	const SHIFTLEFT = "\x16";
	const POW = "\x17";
	const SHIFTRIGHT = "\x18";
	const SMALLER = "\x19";
	const SMALLEROREQUAL = "\x1A";
	const SPACESHIP = "\x1B";
	const NOTEQUAL = "\x1C";
	const ABC = "\x1D";
	//const STRING = "\x1F";
	//const ABC = "\x1";
	//const ABC = "\x1";


	//Stmt
	const PRINT = "\xA0";
	const JMP = "\xA1";//JMPZ int...? //relative
	const JMPZ = "\xA2";//JMPZ READV === 0 INT size offset ...

	const LABEL = "\xA3";//LABEL INT 1 1
	const LGOTO = "\xA4";//GOTOL INT 1 1
	const JMPA = "\xA5";//JMPA INT 1 255

}