'use client'
import React, { ChangeEvent, useState } from 'react'
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'


const PRODUCTS_SEARCH = gql`
  query searchdata($page: Int!, $keyword: String!) {
    searchdataProducts(page: $page, keyword: $keyword) {    
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

  const toDecimal = (number: number) => {
  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  return formatter.format(number);
};

interface SearchDataResponse {
  searchdataProducts: {
    collection: any[];
    paginationInfo: {
      currentPage: number;
      lastPage: number;
      totalCount: number;
    };
  };
}
const Productsearch = () => {
    const [prodsearch, setProdsearch] = useState<any[]>([]);
    const [message, setMessage] = useState('');
    const [searchkey, setSearchkey] = useState('');
    const [page, setPage] = useState<any>(1);
    const [totalpage, setTotalpage] = useState<any>(0);
    const [totalrecords, setTotalrecords] = useState<any>(0);

    async function getProdsearch(event: ChangeEvent<HTMLFormElement>) {
        event.preventDefault();
        setMessage("please wait...");

      try {
        const { data } = await client.query<SearchDataResponse>({
          query: PRODUCTS_SEARCH,
          variables: { 
            page: page,
            keyword: searchkey
          },
        });
        if (data?.searchdataProducts) {
          setProdsearch(data?.searchdataProducts.collection);
          setPage(data?.searchdataProducts.paginationInfo.currentPage);
          setTotalpage(data?.searchdataProducts.paginationInfo.lastPage);
          setTotalrecords(data?.searchdataProducts.paginationInfo.totalCount);
        }
        setTimeout(() => { setMessage(''); }, 1000);
      } catch (err: any) {
        setMessage(err.message);
        setTimeout(() => { setMessage(''); setSearchkey('');}, 1000);
      }
    }

    const searchProducts = async (pg: number) => {
      try {

        const { data } = await client.query<SearchDataResponse>({
          query: PRODUCTS_SEARCH,
          variables: { 
            page: pg,
            keyword: searchkey
          },
        });

        if (data?.searchdataProducts) {
          setProdsearch(data?.searchdataProducts.collection);
          setPage(data?.searchdataProducts.paginationInfo.currentPage);
          setTotalpage(data?.searchdataProducts.paginationInfo.lastPage);
          setTotalrecords(data?.searchdataProducts.paginationInfo.totalCount);
          setTimeout(() => { setMessage(''); setSearchkey('');}, 1000);
        }
      } catch (err: any) {
          setMessage(err.message);          
          setTimeout(() => { setMessage(''); setSearchkey('');}, 1000);
      }
    }

    function firstPage() {
        let pg = page;
        pg = 1;        
        searchProducts(pg);
        return;    
      }
    
      function nextPage() {
        if (page === totalpage) {
            return;
        }
        let pg = page;
        pg++;
        setPage(pg);
        searchProducts(pg);
        return;
      }
    
      function prevPage() {
        if (page === 1) {
          return;
          }
          let pg = page;
          pg--;
          searchProducts(pg);
          return;    
      }
    
      function lastPage() {
        let pg = page;
        pg = totalpage;
        searchProducts(pg);
        return;    
      }

  return (
    <div className="container-fluid bg-dark mb-9">
      <div className='container bg-dark'>
        <h2 className='text-white'>Products Search</h2>
        <div className='text-warning'>{message}</div>
        <form onSubmit={getProdsearch} autoComplete='off'>
            <div className="col-auto">
              <input type="text" required value={searchkey} onChange={e => setSearchkey(e.target.value)} className="form-control-sm" placeholder="enter Product keyword"/>
            </div>
            <div className="col-auto">
              <button type="submit" className="btn btn-primary mt-2 btn-sm mb-3">search</button>
            </div>
        </form>
        <div className="container mb-9">
          <div className="card-group">
        {prodsearch.map((item) => {
                return (
                <div key={item['id']}  className='col-md-4'>
                <div className="card mx-3 mt-3">
                   <div className="card-img-top product-size">
                    <img src={  `https://127.0.0.1:8000/products/${item['productpicture']}` }  alt={""}/>
                    </div>
                    <div className="card-body">
                      <h5 className="card-title">Descriptions</h5>
                      <p className="card-text desc-h">{item['descriptions']}</p>
                    </div>
                    <div className="card-footer">
                      <p className="card-text text-danger"><span className="text-dark">PRICE :</span>&nbsp;<strong>&#8369;{toDecimal(item['sellprice'])}</strong></p>
                    </div>  
                </div>
                
                </div>
          );    
        })}
          </div>         

        { totalrecords > 5 ?
        <>
        <br/>        
        <nav aria-label="Page navigation example">
        <ul className="pagination">
          <li className="page-item"><button type="button" onClick={lastPage} className="page-link" >Last</button></li>
          <li className="page-item"><button type="button" onClick={prevPage} className="page-link" >Previous</button></li>
          <li className="page-item"><button type="button" onClick={nextPage} className="page-link" >Next</button></li>
          <li className="page-item"><button type="button" onClick={firstPage} className="page-link" >First</button></li>
          <li className="page-item page-link text-danger">Page&nbsp;{page} of&nbsp;{totalpage}</li>

        </ul>
        </nav>
        <div className='text-white mt-3'>&nbsp;&nbsp;&nbsp;TOTAL RECORDS : {totalrecords}</div>
        <br/><br/><br/>
        </>
        :
        null
        }

        </div>


        {/* <Footer/> */}
        </div>
    </div>
  )
}

export default Productsearch;