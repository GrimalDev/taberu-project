import mysql from 'mysql';
import fetch from 'node-fetch';

const generalInformations = {
  totalRecipes: 0,
  totalCost: 0,
};

// Function to fetch an API and return its value
// Call OpenAI API#
async function callOpenAiApi(countries = ['thailand', 'china', 'japan', 'india']) {
  //multiline complex text
  if (countries.length === 0) {
    countries = ['thailand', 'china', 'japan', 'india'];
  }
  const ask = `
        Generer une recette de cuisine asiatique Vegan.
        La recette doit être soit forme de JSON et doit contenir les informations suivantes:
          - Titre de la recette
          - Description de la recette
          - Corp de la recette
          - Pays d'origine de la recette (voir liste ci-dessous)
        
        Liste des pays possibles:
        ${countries.map(c => `- ${c}`).join('\n')}
        
        Le corp d'une recette est composé d'un premier bloque de texte qui est la liste des ingrédients.
        Le second bloc est la liste des étapes de la recette.
        
        Exemple de requête:
        {
          "title": "Recette de cuisine",
          "description": "Description de la recette",
          "recipe_body": "Liste des ingrédients etSX Liste des étapes",
          "country": "Pays d'origine de la recette"
        }
        Respecte les clefs utilisées dans l'exemple.
        le titre à pour clée "title" dans le JSON.
        La description à pour clée "description" dans le JSON.
        Le corp de la recette à pour clée "recipe_body" dans le JSON.
        Le pays d'origine de la recette à pour clée "country" dans le JSON (selon la liste).
        
        La recette ne dois pas contenir ni de viande, ni de poisson, ni de produit laitier, ni d'oeuf car elle est végétalienne.
        Soit créatif dans la façon de remplacer ces ingrédients.
        La recette doit être en français.
                
        Merci de ne répondre que la requête sql sans phrase d'introduction.
        Marci de ne répondre qu'une seule recette.
    `;

  const raw = JSON.stringify({
    "model": "gpt-3.5-turbo",
    "messages": [{
      role: 'user',
      content: ask
    }],
    "temperature": 0.6,
    "max_tokens": 1024,
    "stream": false
  });

  const requestOptions = {
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
    response = await fetch("https://api.openai.com/v1/chat/completions", requestOptions);
  } catch (error) {
    console.error('Error fetching API: ', error);
    return;
  }

  //display price
  const responseJSON = await response.json();
  if (responseJSON.usage !== undefined) {
    const tokenConsumed = responseJSON.usage.total_tokens;
    let price = 0.002 * tokenConsumed / 1000;
    //take 4 decimals
    price = Math.round(price * 10000) / 10000;

    //increase total cost
    generalInformations.totalCost += price;

    console.log(`API call cost: ${price} $`);
  }

  return responseJSON;
}

//main function
async function generateRecipes(nRecipes = 1, countries = ['thailand', 'china', 'japan', 'india']) {

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
    apiCallResult = await callOpenAiApi(countries);

    try {

      if (apiCallResult.choices.length === 0) {
        console.error('No recipe generated');
        continue;
      }

      const recipeStr = await apiCallResult.choices[0].message.content;

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
      console.log(apiCallResult);
    }

    //save the recipe to the database
    try {
      await saveRecipes([sqlRecipe]);
    } catch (e) {
      console.error('Error saving recipe -- To be fixed by the administrator');
    }
  }

  //increase total recipes
  generalInformations.totalRecipes += nRecipes;
}

async function saveRecipes(recipesArray) {

// Create a connection to the database
  const connection = await mysql.createConnection({
    host: 'srv.grimaldev.local',
    user: 'taberu-com',
    password: process.env.DB_PASSWORD,
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
  for (const recipe of recipesArray) {

    //verify every field is present and not empty else ignore the recipe

    const currentRecipe = await recipe;

    //if recipe_body is missing ignore the recipe
    if (currentRecipe.recipe_body === undefined || currentRecipe.recipe_body.length === 0) {
      console.error('Recipe body is missing');
      //remove recipe from array
      recipesArray.splice(recipesArray.indexOf(currentRecipe), 1);

      console.log(await currentRecipe);

      continue;
    }

    //if country is missing ignore the recipe
    if (currentRecipe.country === undefined || currentRecipe.country.length === 0) {
      console.error('Recipe country is missing');
      //remove recipe from array
      recipesArray.splice(recipesArray.indexOf(currentRecipe), 1);

      continue;
    }

    //if title is missing add a default title
    if (currentRecipe.title === undefined || currentRecipe.title.length === 0) {
      currentRecipe.title = `Untitled recipe`;
    }

    //if description is missing add a default description
    if (recipe.description === undefined || currentRecipe.description.length === 0) {
      currentRecipe.description = `No description`;
    }

    //add the recipe to the query
    query += `('${currentRecipe.title}', '${currentRecipe.description}', '${currentRecipe.recipe_body}', '${currentRecipe.country}', 'AI'),`;
  }

  //if no query is present, stop the function (query empty if nothing after 'VALUES keyword')
  if (query.split('VALUES')[1].length === 0) {
    console.error('No recipe to save');
    connection.end();
    return;
  }

  //remove the last comma from the query
  query = query.slice(0, -1);

  //execute the query
  await connection.query(query, async function (err, result) {
    if (err) {
      console.error('Error saving recipe, ignoring -- this error is to be fixed by the administrator');
    }

    console.log('Recipe saved');
  });

// Close the database connection
  connection.end();
}


async function main() {
  //call openai handling function with the prompt to generate recipes

  const contries = ['japan', 'china', 'india', 'thailand'];

  for (const country of contries) {
    console.log(`-----------------\nGenerating recipes for ${country}`);
    await generateRecipes(5, [country]);
  }

  console.table(generalInformations);
}

await main();