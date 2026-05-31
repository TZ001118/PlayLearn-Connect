# PlayLearn User Guide

PlayLearn is a PHP and MySQL educational game platform for children aged 4 to 12. The system includes a student game portal, parent dashboard, admin dashboard, game review tools, learning analytics, and a package-based game upload workflow.

## System Roles

### Student

Students use the platform to:

1. Browse and play educational games.
2. View profile information.
3. Check recent game history.
4. See parent focus goals.
5. Leave game reviews.
6. View leaderboard records.
7. Use avatar frames on supported profile and ranking pages.

### Parent

Parents use the platform to:

1. Create child accounts.
2. Link children to the parent account.
3. View child learning progress.
4. Review learning skill charts.
5. Set focus goals for children.
6. Check child history and performance trends.

### Admin

Admins use the platform to:

1. Manage games.
2. Upload package-based games.
3. Manage game categories, age bands, and learning skill weights.
4. View users, parents, children, and linked account details.
5. Ban accounts with a required reason.
6. Inspect game records and learning metrics.
7. Monitor reviews.
8. Delete unsuitable reviews.
9. Manage maintenance mode.

## Main Entry Pages

| Area | Page |
| --- | --- |
| Student home | `homepage.php` |
| Student profile | `student_profile.php` |
| Parent dashboard | `ParentDashboard.php` |
| Admin dashboard | `admin_dashboard.php` |
| Admin game management | `admin_games.php` |
| Admin user analytics | `admin_users.php` |
| Admin score records | `admin_scores.php` |
| Admin review monitor | `admin_reviews.php` |

## Account Flow

1. Public signup is for parent accounts.
2. Child accounts are created from the parent dashboard.
3. Child accounts must be linked to a parent account.
4. Child birthday is locked after creation.
5. Child accounts are limited to ages 4 to 12.
6. Parent-created child accounts can store IC / ID information.
7. Admins can inspect parent-child links from User Analytics.

## Student Usage

1. Log in with a student account.
2. Open the student home page.
3. Select a game card.
4. Play the game.
5. Submit or finish the game so the score is recorded.
6. Open the profile page to check recent records, parent focus goals, and avatar frame display.
7. Use the review page to leave game feedback when needed.

## Parent Usage

1. Log in with a parent account.
2. Open the parent dashboard.
3. Create or review linked child accounts.
4. Use the dashboard cards and charts to check child learning progress.
5. Set focus goals for a child when a specific ability needs more practice.
6. Review recent history and learning analytics before adjusting goals.

## Admin Usage

1. Log in with an admin account.
2. Open the admin dashboard.
3. Use Game Management to add, edit, delete, or upload games.
4. Use User Analytics to inspect account details, parent-child links, IC / ID, game records, reviews, and ban information.
5. Use Player Scores to inspect game records, accuracy, reaction time, and related learning metrics.
6. Use Reviews to monitor review data and delete unsuitable comments.
7. Use Admin Settings to switch the interface theme or manage maintenance mode.

## Game Package Upload

Admins can upload a game package from the Game Management page. A package can be uploaded as a zip file or as an unpacked folder.

Required package files:

1. `manifest.json`
2. `index.html`

Recommended package files:

1. `cover.svg`, `cover.png`, or `cover.webp`
2. CSS files
3. JavaScript files
4. Image files
5. Audio files
6. JSON data files

Uploaded game packages cannot contain PHP files. Package-based games should be built with HTML, CSS, and JavaScript.

## Manifest Format

Each package uses `manifest.json` to describe the game.

```json
{
  "title": "Pattern Sprint",
  "slug": "pattern-sprint",
  "description": "A short game description.",
  "category": "Logic",
  "difficulty": "Easy",
  "age": {
    "min": 7,
    "max": 12
  },
  "skills": {
    "math": 10,
    "logic": 40,
    "memory": 20,
    "focus": 20,
    "speed": 10,
    "creativity": 0
  },
  "entry": "index.html",
  "cover": "cover.svg"
}
```

## PlayLearn Game SDK

Package-based games should load the SDK:

```html
<script src="/assets/js/playlearn-game-sdk.js"></script>
```

Use the SDK to submit score and learning metrics:

```js
PlayLearnGame.submitScore({
  score: 500,
  levelReached: 3,
  correctAnswers: 8,
  totalQuestions: 10,
  reactionTimeMs: 1200,
  durationSeconds: 90
});
```

The SDK sends the current game id and score data to `save_score.php`.

## Learning Analytics

PlayLearn tracks six learning skills:

1. Math
2. Logic
3. Memory
4. Focus
5. Speed
6. Creativity

Each game can train multiple skills at the same time. Admins can set skill weights for each game. Parent analytics use game records, score, accuracy, reaction time, duration, play count, and skill weights to estimate learning performance.

## Database Setup

1. Import the main database dump:

```sql
if0_41736380_playlearn_db.sql
```

2. Run the learning analytics migration:

```sql
migrations/2026_05_learning_analytics.sql
```

3. Check `db_conn.php` before deployment and confirm that the database credentials match the target server.

## Local Setup

1. Place the project inside a PHP web root.
2. Create the MySQL database.
3. Import the database dump.
4. Run the migration file.
5. Start Apache and MySQL.
6. Open the project in a browser.

## Code Size

The project contains approximately 13,500 lines of custom PHP, HTML, CSS, and JavaScript code, excluding third-party libraries, uploaded assets, generated package files, and template-only files.
