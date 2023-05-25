import mysql from 'mysql';
import fetch from 'node-fetch';

// Function to fetch an API and return its value
// Call OpenAI API#
async function callOpenAiApi() {
    //multiline complex text
    const ask = `
        Generer une recette de cuisine asiatique Vegan.
        La recette doit être soit forme de JSON et doit contenir les informations suivantes:
          - Titre de la recette
          - Description de la recette
          - Corp de la recette
          - Pays d'origine de la recette (voir liste ci-dessous avce correspondance à droite une recette de Thaïland devient 'thailand')
        
        Liste des pays possibles:
        Thaïlande : 'thailand'
        Chine : 'china'
        Japon : 'japan'
        Inde : 'india'
        
        Le corp d'une recette est composé d'un premier bloque de texte qui est la liste des ingrédients.
        Le second bloc est la liste des étapes de la recette.
        
        Exemple de requête:
        {
          "title": "Recette de cuisine",
          "description": "Description de la recette",
          "recipe_body": "Liste des ingrédients\n\nListe des étapes",
          "country": "thailand"
        }
        
        La recette ne dois pas contenir ni de viande, ni de poisson, ni de produit laitier, ni d'oeuf car elle est végétalienne.
        Soit créatif dans la façon de remplacer ces ingrédients.
        La recette doit être en français.
                
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
async function generateRecipes(nRecipes = 1) {

  function escapeSpecialCharacters(text) {
    const specialCharacters = [
      '\\', '^', '$', '.', '|', '?', '*', '+', '(', ')', '[', ']', '{', '}', '-'
    ];

    return text.replace(/[\\^$.*+?()[\]{}-]/g, '\\$&');
  }

  console.log(`Preparing ${nRecipes} recipes`);
  
  // Execute the SQL query 10 times to sync the API calls
  for (let i = 0; i < nRecipes; i++) {

      console.log(`Getting recipe ${i + 1}`);
      
      let sqlRecipe = {};

      let apiCallResult = "";

      if (i > 0) {
          console.log('Waiting 1 seconds backoff');
          await new Promise(r => setTimeout(r, 1000));
      }

      console.log('Calling API');

      // Fetch the API
      apiCallResult = await callOpenAiApi();


      try {

            const recipeStr = apiCallResult.choices[0].text;

            //isolate the body from "\"recipe_body\": \"" to the next "\","
            const recipeStrBody = recipeStr.split('"recipe_body": "')[1].split('",')[0];
            //escape the body to correct new line errors
            const recipeStrBodyEscaped = recipeStrBody.replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/\t/g, "\\t").replace(/\f/g, "\\f").replace(/\v/g, "\\v").replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/\\/g, "\\\\");

            //put back the recipe body in the string
            const recipeStrEscaped = recipeStr.replace(recipeStrBody, recipeStrBodyEscaped);

            // Add the result to the array as a json object
            sqlRecipe = JSON.parse(recipeStrEscaped);
       } catch (e) {
            console.error('Error converting to JSON: ', e);
        }

        //save the recipe to the database
        try {
          await saveRecipes([sqlRecipe]);
        } catch (e) {
          console.error('Error saving recipe: ', e);
        }
    }
}

async function saveRecipes(recipesArray) {

// Create a connection to the database
  const connection = await mysql.createConnection({
    host: 'srv.grimaldev.local',
    user: 'taberu-com',
    password: 'jsm8kD3U8GGWW69G',
    database: 'taberu-project'
  });

// Connect to the database
  try {
    connection.connect();
  } catch (e) {
    console.error('Error connecting to database: ', e)
  }

  //prepare sql querry
  let query = 'INSERT INTO recettes (title, description, recipe_body, country, addedby) VALUES';

  //loop through the array and save each recipe
  recipesArray.forEach(recipe => {
    //verify every field is present and not empty else ignore the recipe
    if (recipe.title || recipe.description || recipe.recipe_body || recipe.country) {

      //add the recipe to the query
      query += `('${recipe.title}', '${recipe.description}', '${recipe.recipe_body}', '${recipe.country}', 'AI'),`;

    } else {
      console.error('Recipe is missing a field');
    }
  });

  //if no query is present, stop the function (query empty if nothing after 'VALUES keyword')
  if (query.split('VALUES')[1].length === 0) {
    console.error('No recipe to save');
    connection.end();
    return;
  }

  //remove the last comma from the query
  query = query.slice(0, -1);

  //execute the query
  await connection.query(query, function (err, result) {
    if (err) throw err;
    console.log("Number of records inserted: " + result.affectedRows);
  });

// Close the database connection
  connection.end();
}


async function main() {
  //call openai handling function with the prompt to generate recipes
  await generateRecipes(5);
}

await main();