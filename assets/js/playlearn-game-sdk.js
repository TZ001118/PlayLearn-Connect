(function () {
    const params = new URLSearchParams(window.location.search);
    const context = {
        gameId: parseInt(params.get('playlearn_game_id') || params.get('game_id') || '0', 10),
        gameName: params.get('playlearn_game_name') || '',
    };

    function normalizePayload(payload) {
        payload = payload || {};
        return {
            game_id: context.gameId || payload.game_id || 0,
            game_name: context.gameName || payload.game_name || '',
            score: Number(payload.score || 0),
            level_reached: Number(payload.levelReached || payload.level_reached || 1),
            correct_answers: payload.correctAnswers ?? payload.correct_answers ?? null,
            total_questions: payload.totalQuestions ?? payload.total_questions ?? null,
            reaction_time_ms: payload.reactionTimeMs ?? payload.reaction_time_ms ?? null,
            duration_seconds: payload.durationSeconds ?? payload.duration_seconds ?? null,
        };
    }

    window.PlayLearnGame = {
        context,
        submitScore(payload) {
            return fetch('/save_score.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(normalizePayload(payload)),
            }).then(response => response.json());
        },
    };
})();
