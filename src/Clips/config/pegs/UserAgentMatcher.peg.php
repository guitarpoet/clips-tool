<?php namespace Clips\Libraries; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");
use hafriedlander\Peg\Parser;

class UserAgentMatcher extends Parser\Basic {
/*!* UserAgentMatcher
DLR: '$'
LB: '['
RB: ']'
QUOTE: /['"]/
QM: '?'
ANY: /./
Word: /[a-zA-Z_]/
Number: /[0-9]/
VersionNumber: ( Number+ . )? Number+
Name: ( Word+ > )* Word+

Operator: '>' | '<' | '>=' | '<=' | '~' 
Browser: Name
Platform: '{' > Name > '}'
Device: '(' > Name > ')'
VersionMatcher: Operator > VersionNumber
VersionBetweenMatcher: VersionNumber > '~' > VersionNumber
VersionOp: VersionMatcher | VersionBetweenMatcher
Version: '[' > (VersionOp > ',' > )* VersionOp ']'
Expr: Browser (> Playtform)? (> Device)? (> Version)?
*/
}
