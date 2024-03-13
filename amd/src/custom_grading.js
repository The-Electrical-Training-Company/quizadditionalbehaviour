define(['jquery', 'core/yui', 'core/notification', 'core/ajax', 'core/str'], function ($, Y, notification, ajax, str) {
    let actionGrade = function (selector) {
        let attempt = $(selector).attr('data-attempt');
        let slot = $(selector).attr('data-slot');

        $(selector).find('#submitbutton').attr('disabled', false);

        // Load the form fields.
        let comment = '';
        let commentFormat = '';
        let grade = '';

        $(selector).find('form').serializeArray().forEach(function (element) {
            if (element.name.endsWith('-comment')) {
                comment = element.value;
            }
            if (element.name.endsWith('-commentformat')) {
                commentFormat = element.value;
            }
            if (element.name.endsWith('-mark')) {
                grade = element.value;
            }
        });

        ajax.call([{
            methodname: 'local_quizadditionalbehaviour_set_custom_grading',
            args: { slot: slot, attemptid: attempt, comment: comment, commentformat: commentFormat, grade: grade },
            done: function (result) {
                if (result.result == 'error') {
                    str.get_string('customgradingerror', 'local_quizadditionalbehaviour').then(function(errorMessage) {
                        $(selector).find(`[data-region='errormessagebox']`).html(errorMessage);
                    });
                } else {
                    ajax.call([{
                        methodname: 'local_quizadditionalbehaviour_get_custom_grading',
                        args: { slot: slot, attemptid: attempt },
                        done: function (result) {
                            $(selector).find(`[data-region='errormessagebox']`).html('');
                            $(selector).find('#showbutton').click();
                            $(selector).find(`[data-region='commentbox']`).html(result.comment);
                            $(selector).find('#submitbutton').removeAttr('disabled');
                            $(`.que#q ${slot} .info .state`).html(result.statestring);
                            $(`.que#q ${slot} .info .grade`).html(result.grade);
                            $(`.path-mod-quiz #mod_quiz_navblock #quiznavbutton ${slot}`).attr('class', result.stateclass);
                            $(`.path-mod-quiz #mod_quiz_navblock #quiznavbutton ${slot}`).attr('title', result.statestring);
                            $(`table.quizreviewsummary th:contains('Marks')`).next().html(result.summarymark);
                            $(`table.quizreviewsummary th:contains('Grade')`).next().html(result.summarygrade);
                        },
                        fail: notification.exception
                    }]);
                }
            },
            fail: notification.exception
        }]);
    };

    let customGrading = function (selector) {
        this._regionSelector = selector;
        this._region = $(selector);

        $(selector).find('form').submit(function () {
            actionGrade(selector);
        });

        $(selector).find('#submitbutton').on('click', function () {
            actionGrade(selector);
        });
    };

    customGrading.prototype._regionSelector = null;
    customGrading.prototype._region = null;

    return customGrading;
});