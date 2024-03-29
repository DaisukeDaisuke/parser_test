<?php

namespace purser;

class code{
	//metadata
	//int
	public const TYPE_BYTE = 1;
	public const TYPE_SHORT = 2;
	public const TYPE_INT = 4;
	public const TYPE_LONG = 8;
	public const TYPE_DOUBLE = 9;
	public const TYPE_SIZE_DOUBLE = 8;
	//bool
	public const TYPE_FALSE = 0;
	public const TYPE_TRUE = 1;
	public const TYPE_NULL = 2;
	//type bool

	//cast
	public const TYPE_STRING = 16;
	public const TYPE_BOOL = 17;
	public const TYPE_ARRAY = 18;
	public const TYPE_OBJECT = 19;
	public const TYPE_UNSET = 20;


	//opcode

	public const NOP = "\x00";//(string) null or nop

	//valueOP(Scalar)
	public const READV = "\x91";//readv $a(0)
	public const WRITEV = "\x92";//writev output int size 1
	public const INT = "\x93";
	public const STRING = "\x94";
	public const VALUE = "\x95";
	public const BOOL = "\x96";
	//
	public const ISSET = "\x97";//ISSET output address: bool
	//public const DOUBLE = "\xC4";
	//public const READV = "\x00";

	//binaryOP
	public const ADD = "\x02";//add 80 1000 $a
	public const MUL = "\x03";
	public const DIV = "\x04";
	public const MINUS = "\x05";
	public const B_AND = "\x06";
	public const B_OR = "\x07";
	public const B_XOR = "\x08";
	public const BOOL_AND = "\x09";
	public const BOOL_OR = "\x0A";
	public const COALESCE = "\x0B";
	public const CONCAT = "\x0C";

	public const EQUAL = "\x0D";
	public const GREATER = "\x0E";
	public const GREATEROREQUAL = "\x0F";

	public const IDENTICAL = "\x10";
	public const L_AND = "\x11";
	public const L_OR = "\x12";
	public const L_XOR = "\x13";
	public const MOD = "\x14";
	public const NOTIDENTICAL = "\x15";
	public const SHIFTLEFT = "\x16";
	public const POW = "\x17";
	public const SHIFTRIGHT = "\x18";
	public const SMALLER = "\x19";
	public const SMALLEROREQUAL = "\x1A";
	public const SPACESHIP = "\x1B";
	public const NOTEQUAL = "\x1C";
	public const ABC = "\x1D";
	//public const STRING = "\x1F";
	//public const ABC = "\x1";
	//public const ABC = "\x1";


	//Stmt
	public const PRINT = "\xA0";
	public const JMP = "\xA1";//JMPZ int...? //relative
	public const JMPZ = "\xA2";//JMPZ READV === 0 INT size offset ...
	public const LABEL = "\xA3";//LABEL INT 1 1
	public const LGOTO = "\xA4";//GOTOL INT 1 1
	public const JMPA = "\xA5";//JMPA INT 1 255
	public const SJMP = "\xA6";//JMPZ int...? //relative

	public const EXIT = "\xA7";//exit exit code
	public const CAST = "\xA8";//CAST output TO(byte) scalar

	public const FUN_INIT = "\xB0";//FUN_INIT string size ... target
	public const FUN_SEND_ARGS = "\xB1";//FUN_SEND_ARGS scalar
	public const FUN_SUBMIT = "\xB2";//FUN_SUBMIT output
	//public const FUN_RECEIVE_RETURN = "\xB2";//FUN_RECEIVE_RETURN output

    public const ARRAY_CONSTRUCT = "\xB3";
	public const ARRAY_SET = "\xB4";
	public const ARRAY_GET = "\xB5";//INDIRECT
	public const ARRAY_APPEND = "\xB6";
}