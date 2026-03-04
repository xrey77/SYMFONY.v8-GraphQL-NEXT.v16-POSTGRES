import { ApolloClient, HttpLink, InMemoryCache, gql } from "@apollo/client";
// import { ApolloProvider } from "@apollo/client/react";

export const client = new ApolloClient({
  link: new HttpLink({ uri: "https://127.0.0.1:8000/api/graphql" }),
  cache: new InMemoryCache(),
});



// import { HttpLink } from "@apollo/client";
// import { registerApolloClient, ApolloClient, InMemoryCache } from "@apollo/client-integration-nextjs";

// export const { getClient, query, PreloadQuery } = registerApolloClient(() => {
//   return new ApolloClient({
//     cache: new InMemoryCache(),
//     link: new HttpLink({
//       uri: "https://localhost:3000/graphql", 
//        fetchOptions: { cache: "no-store" },
//     }),
//   });
// });
