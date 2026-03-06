'use client'


import { useState, useEffect } from "react";
import { client } from '@/lib/ApolloClient';
import { gql } from '@apollo/client'

const PRODUCTS_CATALOG = gql`
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

const toDecimal = (number: any) => {
  const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  return formatter.format(number);
};

const Productcatalog = () => {
    const [page, setPage] = useState<number>(1);
    const [totpage, setTotpage] = useState<number>(0);
    const [totrecords, setTotalrecords] = useState<number>(0);
    const [products, setProducts] = useState<any[]>([]);
    const [message, setMessage] = useState<string>('');

    const fetchCatalog = async (pg: number) => {
      try {

        const { data } = await client.query({
          query: PRODUCTS_CATALOG,
          variables: { 
            page: pg,
          },
        });

        if (data.listdataProducts) {
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
      fetchCatalog(page)
    },[]);

    function firstPage(event: any) {
        event.preventDefault()
        let pg = page;
        pg = 1;        
        fetchCatalog(pg);
        return;    
      }
    
      function nextPage(event: any) {
        event.preventDefault()
        if (page === totpage) {
            return;
        }
        let pg = page;
        pg++;
        setPage(pg);
        fetchCatalog(pg);
        return;
      }
    
      function prevPage(event: any) {
        event.preventDefault()
        if (page === 1) {
          return;
          }
          let pg = page;
          pg--;
          fetchCatalog(pg);
          return;    
      }
    
      function lastPage(event: any) {
        event.preventDefault()
        let pg = page;
        pg = totpage;
        fetchCatalog(pg);
        return;    
      }



    return(
    <div className="container-fluid bg-dark mb-9">
            <h3 className="text-warning">Products Catalog</h3>
            <div className="text-danger">{message}</div>
            <div className="card-group mb-3">
            {products.map((item) => {
                    return (
                      <div className='col-md-4' key={item['id']}>
                      <div key={item.id} className="card mx-3 mt-3">
                          <img src={`https://127.0.0.1:8000/products/${item['productpicture']}`} className="card-img-top" alt=""/>
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

        <div className='container-fluid'>
        <nav aria-label="Page navigation example">
        <ul className="pagination">
          <li className="page-item"><a onClick={lastPage} className="page-link sm" href="/#">Last</a></li>
          <li className="page-item"><a onClick={prevPage} className="page-link sm" href="/#">Previous</a></li>
          <li className="page-item"><a onClick={nextPage} className="page-link sm" href="/#">Next</a></li>
          <li className="page-item"><a onClick={firstPage} className="page-link sm" href="/#">First</a></li>
          <li className="page-item page-link text-danger sm">Page&nbsp;{page} of&nbsp;{totpage}</li>
        </ul>
      </nav>
      <div className='text-white'>&nbsp;&nbsp;&nbsp;TOTAL RECORDS : {totrecords}</div>

      <br/><br/>
      </div>
      {/* <Footer/> */}
  </div>
  )
}

export default Productcatalog;