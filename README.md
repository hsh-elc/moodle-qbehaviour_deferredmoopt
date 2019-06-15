# moodle-qbehaviour_deferredprogrammingtask

This behaviour is only intended to be used with programming task questions.
If you want to use multiple types of questions in a quiz (other than programming task questions), don't use this behaviour. Programming task questions will automatically pick the correct bevaviour according to the following algorithm:
- if the selected behaviour is a programming task behaviour, use it
- if the selected behaviours name starts with "immediate", use moodle-qtype_immediateprogrammingtask
- else use moodle-qtype_deferredprogrammingtask

## License ##

2019 ZLB-ELC Hochschule Hannover <elc@hs-hannover.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
