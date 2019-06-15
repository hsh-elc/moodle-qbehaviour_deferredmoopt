# moodle-qbehaviour_deferredprogrammingtask

This behaviour is only intended to be used with programming task questions.
If you want to use multiple types of questions in a quiz (other than programming task questions), don't use this behaviour. Programming task questions will automatically pick the correct bevaviour according to the following algorithm:
- if the selected behaviour is a programming task behaviour, use it
- if the selected behaviours name starts with "immediate", use moodle-qtype_immediateprogrammingtask
- else use moodle-qtype_deferredprogrammingtask
