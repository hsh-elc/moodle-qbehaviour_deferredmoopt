<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Question behaviour for the case when the student's answer is just
 * saved until they submit the whole attempt, and then it is graded.
 *
 * @package    qbehaviour
 * @subpackage deferredfeedback
 * @copyright  2009 The Open University
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Question behaviour for deferred feedback.
 *
 * The student enters their response during the attempt, and it is saved. Later,
 * when the whole attempt is finished, their answer is graded.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredprogrammingtask extends question_behaviour_with_save {

    public function is_compatible_question(question_definition $question) {
        return $question instanceof qtype_programmingtask_question;
    }

    public function get_min_fraction() {
        return $this->question->get_min_fraction();
    }

    public function get_right_answer_summary() {
        return $this->question->get_right_answer_summary();
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('comment')) {
            return $this->process_comment($pendingstep);
        } else if ($pendingstep->has_behaviour_var('finish')) {
            return $this->process_finish($pendingstep);
        } else {
            return $this->process_save($pendingstep);
        }
    }

    /**
     * Only differs from parent implementation in that it sets a  flag on the first execution and
     * doesn't keep this step if the flag has already been set. This is important in the face of regrades.
     * When a submission is regraded the comment and the mark refer to the old version of the grading result,
     * therefore we don't include the comment and the mark in the regrading.
     * @global type $DB
     * @param \question_attempt_pending_step $pendingstep
     * @return bool
     */
    public function process_comment(\question_attempt_pending_step $pendingstep): bool {
        global $DB;
        if ($DB->record_exists('question_attempt_step_data', array('attemptstepid' => $pendingstep->get_id(), 'name' => '-_appliedFlag'))) {
            return question_attempt::DISCARD;
        }

        $parentReturn = parent::process_comment($pendingstep);

        $pendingstep->set_behaviour_var('_appliedFlag', '1');
        return $parentReturn;
    }

    public function process_save(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        } else if (!$this->qa->get_state()->is_active()) {
            throw new coding_exception('Question is not active, cannot process_actions.');
        }

        if ($this->is_same_response($pendingstep)) {
            return question_attempt::DISCARD;
        }

        if ($this->is_complete_response($pendingstep)) {
            $pendingstep->set_state(question_state::$complete);
        } else {
            $pendingstep->set_state(question_state::$todo);
        }
        return question_attempt::KEEP;
    }

    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('comment')) {
            return $this->summarise_manual_comment($step);
        } else if ($step->has_behaviour_var('finish')) {
            return $this->summarise_finish($step);
        } else {
            return $this->summarise_save($step);
        }
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        $response = $this->qa->get_last_step()->get_qt_data();
        if (!$this->question->is_gradable_response($response)) {
            $pendingstep->set_state(question_state::$gaveup);
            $pendingstep->set_fraction($this->get_min_fraction());
        } else {
            $state = $this->question->grade_response_asynch($this->qa);
            $pendingstep->set_state($state);
        }
        $pendingstep->set_new_response_summary($this->question->summarise_response($response));
        return question_attempt::KEEP;
    }

}
