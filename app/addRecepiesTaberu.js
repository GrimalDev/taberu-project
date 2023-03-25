const mysql = require('mysql');
const fetch = require('node-fetch');

// Function to fetch an API and return its value
// Call OpenAI API
async function callOpenAiApi() {
    //multiline complex text
    const ask = `
        Générer une requête SQL INSERT pour cette recette en utilisant les champs suivants de ma table de recettes : title, description, recipes, country et addedby.

        Le titre est limité à 49 caractères. La description est limitée à 100 caractères.
        
        Le nom de la table est : recettes
        Toutes les recettes doivent êtres véganes. Donc sans poisson, produits animalier ou viande. Génère une autre recette d'un des pays que je t'ai donnés.
        Tu indiques le pays choisi selon la table de correspondance et l'indiques dans la requête. Donne des titres précis et des descriptions qui donnent envie. L'utilisateur est GrimalDev. Le stars et a NULL par défaut donc ne le renseigne pas. Ne renseigne pas par l'id et la creation date, ils sont crées automatiquement, les retours à la ligne sont des <br>. Egalement n'utilise que des doubles quotes " pour les textes. La requête sql doit être conforme à l’exemple plus bas.
        
        Voici la liste des pays de recettes possibles :
        
        Thaïlande : 'thailand'
        Japon : 'japan'
        Chine : 'china'
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

    const response = await fetch("https://api.openai.com/v1/completions", requestOptions);
    return response.json();

}

// Create a connection to the database
const connection = mysql.createConnection({
  host: 'srv.grimaldev.local',
  user: 'taberu-com',
  password: 'password',
  database: 'taberu-project'
});

// Connect to the database
connection.connect((error) => {
  if (error) {
    console.error('Error connecting to the database: ', error);
    return;
  }

  console.log('Connected to the database!');

  // Execute the SQL query 10 times
  for (let i = 1; i <= 10; i++) {
        const query = value;

        connection.query(query, (error, results, fields) => {
          if (error) {
            console.error('Error executing query: ', error);
            return;
          }

          console.log('Query results: ', results);
        });
      })
      .catch((error) => {
        console.error('Error fetching data: ', error);
      });
  }

  // Close the database connection
  connection.end((error) => {
    if (error) {
      console.error('Error closing connection: ', error);
      return;
    }

    console.log('Connection closed!');
  });
});
