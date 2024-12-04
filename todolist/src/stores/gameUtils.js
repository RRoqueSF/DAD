import { ref } from 'vue';

const allTiles = import.meta.glob('@/assets/cards/*.png');
console.log('tiles', allTiles);

export function initializeGame(totalTiles) {
  if (totalTiles % 2 !== 0) {
    throw new Error('Total tiles must be even to form pairs.');
  }

  // Get the paths for all available card images, excluding semFace.png
  const allImages = Object.keys(allTiles).filter(
    (imagePath) => !imagePath.includes('semFace.png')
  );

  // Check if we have enough images to match the required tiles
  if (allImages.length < totalTiles / 2) {
    throw new Error('Not enough unique card images available to initialize the game.');
  }

  // Randomly select unique images for the game
  const selectedImages = [];
  while (selectedImages.length < totalTiles / 2) {
    const randomImage = allImages[Math.floor(Math.random() * allImages.length)];
    if (!selectedImages.includes(randomImage)) {
      selectedImages.push(randomImage);
    }
  }

  // Duplicate and shuffle the selected images for pairing
  const pairedImages = [...selectedImages, ...selectedImages];
  for (let i = pairedImages.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [pairedImages[i], pairedImages[j]] = [pairedImages[j], pairedImages[i]];
  }

  // Load tiles with default unturned state and images
  return pairedImages.map((imagePath, index) => ({
    id: index,
    face: imagePath,
    image: allTiles[imagePath], // Dynamically import the image
    isFlipped: false,
    isMatched: false,
  }));
}

export function flipTile(tile, firstTile, secondTile, setProcessing, onMatch, onMismatch) {
    if (tile.isFlipped || tile.isMatched) return; // Ignore already flipped or matched tiles.

    tile.isFlipped = true;

    if (!firstTile.value) {
        // First tile of the pair.
        firstTile.value = tile;
    } else if (!secondTile.value) {
        // Second tile of the pair.
        secondTile.value = tile;
        setProcessing(true); // Disable interactions during evaluation.

        // Check for a match.
        if (firstTile.value.face === secondTile.value.face) {
            firstTile.value.isMatched = true;
            secondTile.value.isMatched = true;
            firstTile.value = null;
            secondTile.value = null;
            setTimeout(onMatch, 500); // Delay for match feedback.
        } else {
            setTimeout(() => {
                firstTile.value.isFlipped = false;
                secondTile.value.isFlipped = false;
                firstTile.value = null;
                secondTile.value = null;
                onMismatch();
            }, 1000); // Delay for mismatch feedback.
        }
    }
}
let timerInterval = null;
export const timer = ref(0); // Reactive timer.

export function startTimer() {
    timer.value = 0;  // Reset the timer to 0
    timerInterval = setInterval(() => {
        timer.value++;
        console.log(timer.value); // Log timer value to check if it's updating
    }, 1000);
}
export function stopTimer() {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}
export function resetGameState() {
    stopTimer();
    timer.value = 0;
}
