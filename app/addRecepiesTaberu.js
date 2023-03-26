// import mysql from 'mysql';
import fetch from 'node-fetch';

// Function to fetch an API and return its value
// Call OpenAI API#
async function callOpenAiApi() {
    //multiline complex text
    const ask = `
        Générer une requête SQL INSERT pour cette recette en utilisant les champs suivants de ma table de recettes : title, description, recipes, country et addedby.

        Le titre est limité à 49 caractères. La description est limitée à 100 caractères.
        
        Le nom de la table est : recettes
        Toutes les recettes doivent êtres véganes. Donc sans poisson, produits animalier ou viande. Génère une autre recette d'un des pays que je t'ai donnés.
        Tu indiques le pays choisi selon la table de correspondance et l'indiques dans la requête. Donne des titres précis et des descriptions qui donnent envie. L'utilisateur est GrimalDev. Le stars et a NULL par défaut donc ne le renseigne pas. Ne renseigne pas par l'id et la creation date, ils sont crées automatiquement, les retours à la ligne sont des <br>. Egalement n'utilise que des doubles quotes " pour les textes. La requête sql doit être conforme à l’exemple plus bas.
        
        Voici la liste des pays de recettes possibles :
        

        Japon : 'japan'

        Inde : 'india'
        
        Exemple de requête:
        INSERT INTO recettes (title, description, recipes, country, addedby) VALUES ("TITRE", "DESCRIPTION", "RECETTE", 'PAYS', 'GrimalDev');
        
        Merci de ne répondre que la requête sql sans phrase d'introduction.
        Marci de ne répondre qu'une seule recette.
    `;

    const raw = JSON.stringify({
        "model": "text-davinci-003",
        "prompt": ask,
        "temperature": 0.6,
        "max_tokens": 1024,
        "stream": false
    });

    var requestOptions = {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${process.env.OPENAI_API_KEY}`
        },
        body: raw,
        redirect: 'follow'
    };

    let response = null;

    try {
        response = await fetch("https://api.openai.com/v1/completions", requestOptions);
    } catch (error) {
        console.error('Error fetching API: ', error);
        return;
    }
    
    return response.json();
}

//main function
async function main() {

    let sqlRecipes = "";

    // Execute the SQL query 10 times to sync the API calls
    for (let i = 0; i < 10; i++) {

        console.log(`Getting recipes ${i + 1}`);

        let apiCallResult = "";

        setTimeout(() => {
            console.log('Waiting 1 second');
        }, 1000);

        // Fetch the API
        try {
            apiCallResult = await callOpenAiApi();

            // Add the result to the array
            sqlRecipes += apiCallResult.choices[0].text;
        } catch (e) {
            console.error('Error fetching API: ', e);
        }

        // connection.query(apiCallResult, (error, results, fields) => {
        //     if (error) {
        //     console.error('Error executing query: ', error);
        //     return;
        //     }

        //     console.log('Query results: ', results);
        // });
    }

    return sqlRecipes;
}

// Create a connection to the database
// const connection = mysql.createConnection({
//   host: 'srv.grimaldev.local',
//   user: 'taberu-com',
//   password: 'jsm8kD3U8GGWW69G',
//   database: 'taberu-project'
// });

// Connect to the database
// connection.connect();

console.log(await main());

// Close the database connection
// connection.end();