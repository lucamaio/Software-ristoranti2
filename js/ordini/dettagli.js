document.addEventListener('DOMContentLoaded', () => {
    ricavaDettagliOrdine();
});

async function ricavaDettagliOrdine(){
    const url = "../../api/orders.php";
    const ID_ordine = window.APP_CONFIG.ID_ordine;
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                azione: "get-dettagli",
                ID_ordine : ID_ordine
            })    
        });

        const result = await response.json();
        console.log(result);
    } catch (error) {
        console.error(error);
    }
}