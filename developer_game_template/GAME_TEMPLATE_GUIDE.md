# PlayLearn Game Package Template

This folder is a working example of a PlayLearn HTML/JS game package.

Admins can upload this folder as a zip package. If the server does not support PHP ZipArchive, upload the unpacked folder files from the Game Management page instead.

## Required files

- `manifest.json`
- `index.html`

## Optional files

- `cover.svg`, `cover.png`, or `cover.webp`
- CSS, JS, image, audio, or JSON assets

## Score submission

Use the SDK:

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

The SDK automatically sends the game id from the PlayLearn player page.
