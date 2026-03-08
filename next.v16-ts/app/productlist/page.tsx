'use client'

import { useEffect, useState } from 'react'
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'

const PRODUCTS_LIST = gql`
  query listdata($page: Int!) {
    listdataProducts(page: $page) {  
        collection {
          id
          category
          descriptions
          qty
          unit
          costprice
          sellprice
          saleprice
          productpicture
          alertstocks
          criticalstocks
        }
        paginationInfo {
          currentPage
          itemsPerPage
          lastPage
          totalCount
        }        
    }
  }
  `

interface Product {
  id: number;
  category: string;
  descriptions: string;
  qty: number;
  unit: string;
  costprice: number;
  sellprice: number;
  saleprice: number;
  productpicture: string;
  alertstocks: number;
  criticalstocks: number;
}

interface ProductListResponse {
  listdataProducts: {
    collection: Product[];
    paginationInfo: {
      currentPage: number;
      itemsPerPage: number;
      lastPage: number;
      totalCount: number;
    };
  };
}

const toDecimal = (number: number) => {
  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  return formatter.format(number);
};

const Productlist = () => {
    const [page, setPage] = useState<number>(1);
    const [totpage, setTotpage] = useState<number>(0);
    const [totrecords, setTotalrecords] = useState<number>(0);

    const [products, setProducts] = useState<any[]>([]);
    const [message, setMessage] = useState<string>('');

    const fetchProducts = async (pg: number) => {
      try {

        const { data } = await client.query<ProductListResponse>({
          query: PRODUCTS_LIST,
          variables: { 
            page: pg,
          },
        });

        if (data?.listdataProducts) {
          setProducts(data.listdataProducts.collection);
          setPage(data.listdataProducts.paginationInfo.currentPage);
          setTotpage(data.listdataProducts.paginationInfo.lastPage);
          setTotalrecords(data.listdataProducts.paginationInfo.totalCount)
        }
      } catch (err: any) {
        alert("error");
        setMessage(err.message);
        setTimeout(() => {
          setMessage('');
        }, 3000);
      }
    }

    useEffect(() => {
      setMessage('please wait...');
      fetchProducts(page);
        setTimeout(() => {
          setMessage('');
        }, 3000);
   },[]);

    function firstPage() {
        let pg = page;
        pg = 1;        
        fetchProducts(pg);
        return;    
      }
    
      function nextPage() {
        if (page === totpage) {
            return;
        }
        let pg = page;
        pg++;
        setPage(pg);
        fetchProducts(pg);
        return;
      }
    
      function prevPage() {
        if (page === 1) {
          return;
          }
          let pg = page;
          pg--;
          fetchProducts(pg);
          return;    
      }
    
      function lastPage() {
        let pg = page;
        pg = totpage;
        fetchProducts(pg);
        return;    
      }

    return(
    <div className="container-fluid h-100 bg-dark"> 
      <div className='container bg-dark'>
            <h1 className='text-white'>Products List</h1>
            <div className='text-warning xtop'>{message}</div>
            <table className="table table-warning table-striped  mt-4">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Descriptions</th>
                <th scope="col">Qty</th>
                <th scope="col">Unit</th>
                <th scope="col">Price</th>
                </tr>
            </thead>
            <tbody>

            {products.map((item) => {
            return (
              <tr key={item['id']}>
                 <td>{item['id'].match(/\d+/)}</td>
                 <td>{item['descriptions']}</td>
                 <td>{item['qty']}</td>
                 <td>{item['unit']}</td>
                 <td><span className='text-danger'>&#8369;</span>{toDecimal( item['sellprice'])}</td>
               </tr>
              );
        })}
            </tbody>
            </table>

        <nav aria-label="Page navigation example">
        <ul className="pagination">
          <li className="page-item"><button type="button" onClick={lastPage} className="page-link" >Last</button></li>
          <li className="page-item"><button type="button" onClick={prevPage} className="page-link" >Previous</button></li>
          <li className="page-item"><button type="button" onClick={nextPage} className="page-link" >Next</button></li>
          <li className="page-item"><button type="button" onClick={firstPage} className="page-link" >First</button></li>
          <li className="page-item page-link text-danger">Page&nbsp;{page} of&nbsp;{totpage}</li>

        </ul>
      </nav>
      <div className='text-white'>TOTAL RECORDS : {totrecords}</div>
      </div>
  </div>
  )
}
export default Productlist;