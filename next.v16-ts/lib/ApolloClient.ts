import { ApolloClient, HttpLink, InMemoryCache, gql } from "@apollo/client";

export const client = new ApolloClient({
  link: new HttpLink({ uri: "https://127.0.0.1:8000/api/graphql" }),
  cache: new InMemoryCache(),
});

