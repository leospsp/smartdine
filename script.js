function navigate(screenId) {
    document.querySelectorAll('.screen').forEach(screen => {
        screen.classList.remove('active');
    });
    document.getElementById(screenId).classList.add('active');
}

function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('open');
}

function viewDish(dish) {
    const dishData = {
        pasta: {
            title: "Pasta",
            image: "pasta.jpg",
            description: "Deliziosa pasta con sugo fresco."
        },
        pizza: {
            title: "Pizza",
            image: "pizza.jpg",
            description: "Gustosa pizza con ingredienti freschi."
        }
    };

    if (dishData[dish]) {
        document.getElementById('dish-title').innerText = dishData[dish].title;
        document.getElementById('dish-image').src = dishData[dish].image;
        document.getElementById('dish-description').innerText = dishData[dish].description;
        navigate('dish');
    }
}

function addToCart() {
    alert("Piatto aggiunto al carrello!");
}
