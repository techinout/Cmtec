ocs)&&(f=null),r(t)){const r=l(f,t);if(r&&n.isMsbQwsDocsCacheEnabled(i)){const i={dataSource:t,results:f,lastUpdatedTime:n.getCurrentTime(),cacheState:n.MsbQfCacheState.ValidResults};n.LightweightStorage.setItem(n.ZqSuggestionsStorageKey,JSON.stringify(i))}}if(p==null)return;if(sb_ct(p),p=null,n.config.msbQwsDocsNoRefreshAfterCachedResults&&b)return}u(t,f,e,o,s,a)};super.fetch(i,g,f,e,c,k,v)}}}getSubstrateBaseUrl(){return _w.BingAtWork&&_w.BingAtWork.wsb&&_w.BingAtWork.wsb.gccRegion===2?vt:at}getSubstrateEventsUrl(){return this.getSubstrateBaseUrl()+"events"}getSubstrateInitUrl(){return this.getSubstrateBaseUrl()+"init"}getSubstrateSuggestionsUrl(){return this.getSubstrateBaseUrl()+"suggestions?query="}getSubstrateSearchUrl(){return this.getSubstrateBaseUrl()+"query"}getSubstrateRecomendationsUrl(){return this.getSubstrateBaseUrl()+"recommendations"}getQwsDocsCachedResults(){let t={results:undefined,cacheState:n.MsbQfCacheState.Initialized,dataSource:undefined,lastUpdatedTime:undefined};const i=n.LightweightStorage.getItem(n.ZqSuggestionsStorageKey);if(!i)return t;try{t=JSON.parse(i)}catch(r){const u=`[MSB.Error]  QWS docs cache is broken.
                            cacheString=${i},
                            error.message=${r===null||r===void 0?void 0:r.message}`;return t.cacheState=n.MsbQfCacheState.EmptyFromParseError,t.lastUpdatedTime=n.getCurrentTime(),n.LightweightStorage.setItem(n.ZqSuggestionsStorageKey,JSON.stringify(t)),t}return t.cacheState==n.MsbQfCacheState.EmptyFromParseError||t.cacheState==n.MsbQfCacheState.EmptyFromExpired?t:(t&&t.dataSource==this._dataSource&&(n.getCurrentTime()-t.lastUpdatedTime<ht&&!(n.TestHookUrlParameters===null||n.TestHookUrlParameters===void 0?void 0:n.TestHookUrlParameters.msbSHCacheExpired)?l(t.results,this._dataSource)?(t.results=t.results,t.cacheState=n.MsbQfCacheState.ValidResults):t.cacheState=n.MsbQfCacheState.NoResults:(t.cacheState=n.MsbQfCacheState.EmptyFromExpired,t.results=undefined,t.lastUpdatedTime=n.getCurrentTime(),n.LightweightStorage.setItem(n.ZqSuggestionsStorageKey,JSON.stringify(t)))),t)}buildParams(t,i,r){var s,u={};if(this._providerType==0&&(u[n.Service.QueryParams.ConversationId]=t[n.Service.QueryParams.ConversationId],u[p]=b,this._authType!=1||r||i!=n.Scope.People?i!=n.Scope.All&&(u[f]=k):u[f]=d,r&&this._authType==1&&t[n.Service.QueryParams.ImpressionGuid]&&(u[n.Service.QueryParams.LogicalId]=t[n.Service.QueryParams.ImpressionGuid]),u[o]=this.getEntityTypes(i,r),i==n.Scope.Documents&&r&&n.config.msbEnableDocumentZQ&&(u[e]=ot)),n.TestHookUrlParameters){let t=(s=n.TestHookUrlParameters["3sflights"])!==null&&s!==void 0?s:"";t&&(u[e]=t);n.TestHookUrlParameters["3sdebug"]&&(u[w]="1")}return u}getEntityTypes(t,i){let r;switch(t){case n.Scope.Documents:r="Documents";break;case n.Scope.People:r="People";break;case n.Scope.All:case n.Scope.Work:let u=[];if(this._authType==1&&u.push("Documents"),!i&&n.RuntimeConfig.QfMode!=5){let t=n.msbHost===null||n.msbHost===void 0?void 0:n.msbHost.isTenantMsbEnabled();t||u.push("People")}r=u.join(",");break;default:throw new Error("Unsupported scope "+t);}return r}instrumentClick(t,i){if(t&&i)