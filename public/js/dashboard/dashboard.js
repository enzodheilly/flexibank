function updateOrderStatus(orderId, newStatus) {
    fetch(`/update-status/${orderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'affichage du statut dans la page (exemple : changer la couleur ou le texte)
            const statusElement = document.getElementById(`status-${orderId}`);
            statusElement.textContent = newStatus;  // Mise à jour du statut affiché
            statusElement.className = `status-${newStatus}`;  // Appliquer la nouvelle classe CSS
        }
    })
    .catch(error => console.error('Erreur lors de la mise à jour du statut', error));
}