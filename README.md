# parser_test
My personal play project.  
Please note that it relies on many dependencies.  
Warning: This is my fun project and there is no warranty of any kind.  
I recommend kphp instead.   
https://github.com/VKCOM/kphp

<details>
<summary>Supported functions, Unsupported features</summary>
<br>
  
## Unsupported features
note: many functions are not supported.
- [ ] class, interface, trait
- [ ] STDIN
- [ ] isset 
- [ ] array
- [ ] goto


## Supported functions
note: many unknown bugs are included.
- [x] print
- [x] echo
- [x] if
- [x] else
- [x] elseif
- [x] for
- [x] while
- [x] switch
- [x] (+,-,*,/, etc...)
- [x] true,false
- [x] Variable (part)
- [x] continue, break
- [x] function call(unsafe)
- [x] cast
- [x] exit
- [x] `__halt_compiler();`
- [x] `$a++`,`++$a`
- [x] `$a--`,`--$a`

</details>

## files
[main_old2.php](https://github.com/DaisukeDaisuke/parser_test/blob/master/src/main_old2.php)

[decoder.php](https://github.com/DaisukeDaisuke/parser_test/blob/master/src/decoder.php)

[opcode_dumper.php](https://github.com/DaisukeDaisuke/parser_test/blob/master/src/opcode_dumper.php)
